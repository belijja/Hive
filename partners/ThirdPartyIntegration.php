<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 10.05.2017
 * Time: 11:46
 */
declare(strict_types = 1);

namespace Partners;

use Configs\ThirdPartyIntegrationCodes;
use Helpers\ServerHelpers\ServerManager;
use Helpers\SoapHelpers\ThirdPartyIntegrationSoapClient;

/**
 * Class ThirdPartyIntegration
 * @package Partners
 */
class ThirdPartyIntegration extends AbstractPartners
{

    private $tpiConfigs;

    /**
     * ThirdPartyIntegration constructor.
     * @param ThirdPartyIntegrationCodes $tpiConfigs
     */
    public function __construct(ThirdPartyIntegrationCodes $tpiConfigs)
    {
        $this->tpiConfigs = $tpiConfigs;
        parent::__construct(new ServerManager(), new ThirdPartyIntegrationSoapClient($this->tpiConfigs));
    }

    /**
     * @param array $arrayOfParams
     * @return array
     */
    public function checkAndRegisterUser(array $arrayOfParams): array
    {
        list($userId, $skinId, $providerId, $soapClient) = $arrayOfParams;
        $returnData = [];
        $query = $this->db->prepare("SELECT poker_skinid FROM provider_skin_mapping WHERE provider_id = :providerId AND provider_skinid = :providerSkinId");
        if (!$query->execute([
                ':providerId'     => $providerId,
                ':providerSkinId' => $skinId
            ]) || $query->rowCount() == 0
        ) {//if query fail
            $returnData['status'] = false;
            return $returnData;
        } else {
            $result = $query->fetchObject();
            $userStatus = $this->checkAndRegisterThirdPartyIntegrationUser($userId, (int)$result->poker_skinid, $soapClient);
            if ($userStatus['status'] == false) {
                $returnData['status'] = false;
                return $returnData;
            } else {
                $returnData['status'] = true;
                return $returnData;
            }
        }
    }

    /**
     * @param int $userId
     * @param int $pokerSkinId
     * @param \SoapClient|null $soapClient
     * @param string|null $userDate
     * @return array
     */
    public function checkAndRegisterThirdPartyIntegrationUser(int $userId, int $pokerSkinId, \SoapClient $soapClient = null, string $userDate = null): array
    {
        $this->tpiConfigs = $this->tpiConfigs->getTpiConfigs($pokerSkinId);
        $needUpdate = false;
        $isFirstLogin = 1;
        $returnData = [];
        if ($userDate != null) {//this whole if statement is executed only on com part
            $this->db->quote($userDate);
            $query = $this->db->prepare("SELECT ud.updatetime < :userDate, c.user_id, (ud.logintime <= ud.acttime) AS isFirst FROM casino_ids c JOIN udata ud ON c.user_id = ud.uid WHERE c.provider_id = :providerId AND c.casino_id = :userId AND c.skin_id = :partnerId");
            $result = $query->execute([
                ':userDate'   => $userDate,
                ':providerId' => $this->tpiConfigs['providerId'],
                //only for com
                ':userId'     => $userId,
                ':partnerId'  => $this->tpiConfigs['partnerId']
                //only for com
            ]);
            if ($result && $query->rowCount() == 1) {
                $result = $query->fetchAll();
                $isFirstLogin = $result[2] ? 1 : 0;
                $needUpdate = $result[0] || $isFirstLogin;
                $returnData = [
                    'status'   => 1,
                    'poker_id' => $result[1]
                ];
            }
        } else {
            $query = $this->db->prepare("SELECT c.user_id, (ud.logintime <= ud.acttime) AS isFirst FROM casino_ids c JOIN udata ud ON c.user_id = ud.uid WHERE c.provider_id = :providerId AND c.casino_id = :userId AND c.skin_id = :partnerId");
            $result = $query->execute([
                ':userDate'   => $userDate,
                ':providerId' => $this->tpiConfigs['providerId'],
                //only for com
                ':userId'     => $userId,
                ':partnerId'  => $this->tpiConfigs['partnerId']
                //only for com
            ]);
        }
        if (/*!*/
        $result
        ) {
            $returnData['status'] = false;
            return $returnData;
        }
        if ($query->rowCount() > 0 && $returnData['status'] === true) {
            $result = $query->fetchObject();
            $isFirstLogin = $result->isFirst ? 1 : 0;
            $returnData = [
                'status'   => 1,
                'poker_id' => $result->user_id
            ];
        }
        if ($query->rowCount() == 0 || $needUpdate) {
            $legacyUserInfo = $this->soapClient->getUserInfo($userId, $pokerSkinId, $soapClient);
            /*if (is_soap_fault($legacyUserInfo) || $legacyUserInfo->UserGetInfoResult->resultCode != 1) {
                $returnData['status'] = false;
                return $returnData;
            }*/
            $user = $legacyUserInfo->UserGetInfoResult;
            $params = [];
            $params['providerId'] = $this->tpiConfigs['providerId'];
            $params['userId'] = $userId;
            $params['skinId'] = $this->tpiConfigs['partnerId'];
            if (isset($user->agentId) && $user->agentId != 0) {
                $query = $this->db->prepare("SELECT * FROM provider_affil_mapping WHERE provider_id = :providerId AND provider_affilid = :agentId");
                $result = $query->execute([
                    ':providerId' => $this->tpiConfigs['providerId'],
                    ':agentId'    => $user->agentId
                ]);
                if ($query->rowCount() != 1) {
                    /*$agentName = $this->db->quote($user->agentName);*/
                    $query = $this->db->prepare("INSERT INTO affiliates (name) VALUES (:agentName)");
                    if ($query->execute([
                        ':agentName' => $user->agentName
                    ])
                    ) {
                        $affiliateId = $this->db->lastInsertId();
                        $query = $this->db->prepare("INSERT INTO provider_affil_mapping (provider_id, provider_affilid, poker_affilid) VALUES (:providerId, :agentId, :affiliateId)");
                        $query->execute([
                            ':providerId'  => $this->tpiConfigs['providerId'],
                            ':agentId'     => $user->agentId,
                            ':affiliateId' => $affiliateId
                        ]);
                    }
                }
                $params['affiliateId'] = $user->agentId;
            }
            if (!$needUpdate) {
                $params['active'] = 1;
                $params['temporaryNick'] = $user->screenName != '' ? 0 : 1;
                $params['username'] = $user->screenName != '' ? $user->screenName : 'player' . mt_rand(1000000, mt_getrandmax());
                $params['password'] = 'invalid password';
                $params['currencyCode'] = isset($user->currency) ? $user->currency : 'EUR';
                $params['flags'] = $user->flags;
                $params['externalUsername'] = $user->username;
            }
            $params['email'] = $user->email;
            $params['firstName'] = $user->firstname;
            $params['lastName'] = $user->lastname;
            $position = strpos($user->country, ':');
            $params['country'] = $position !== false ? substr($user->country, 0, $position) : $user->country;
            if ($position !== false) {
                $params['state'] = substr($user->country, $position + 1);
            }
            if (isset($user->dateOfBirth)) {
                $req['dateOfBirth'] = substr($user->dateOfBirth, 0, 10);
            }
            $params['isFirstLogin'] = $isFirstLogin ? 1 : 0;
            return $this->recursiveCall($needUpdate, $params, $returnData);
        }
        return $returnData;
    }

    /**
     * @param bool $needUpdate
     * @param array $params
     * @param array $returnData
     * @return array
     */
    private function recursiveCall(bool $needUpdate, array $params, array $returnData): array
    {
        $response = $this->serverManager->callExternalMethod($needUpdate ? 'UpdatePokerPlayer' : 'InsertPokerRegistration', $params);
        if (!$response || $response['status'] != 1) {
            if (!$needUpdate && $params['temporaryNick'] == 0 && $response['errorCode'] == 3) {
                $params['temporaryNick'] = 1;
                $params['username'] = "player" . mt_rand(1000000, mt_getrandmax());
                $this->recursiveCall($needUpdate, $params, $returnData);
            }
            return $needUpdate ? $returnData : $response;
        }
        return $response;
    }

}