<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 10.05.2017
 * Time: 11:46
 */
declare(strict_types = 1);

namespace Partners;

use Configs\SkinConfigs;
use Helpers\ServerHelpers\ServerManager;
use Helpers\SoapHelpers\ThirdPartyIntegrationSoapClient;

/**
 * Class ThirdPartyIntegrationPartners
 * @package Partners
 */
class ThirdPartyIntegrationPartners extends AbstractPartners
{

    /**
     * ThirdPartyIntegrationPartners constructor.
     */
    public function __construct()
    {
        parent::__construct(new ServerManager(), new ThirdPartyIntegrationSoapClient());
    }

    /**
     * @param array $arrayOfParams
     * @return void
     * @throws \SoapFault
     */
    public function checkAndRegisterUser(array $arrayOfParams): void
    {
        @list($userId, $skinId, $providerId, $soapClient) = $arrayOfParams;
        $query = $this->db->prepare("SELECT poker_skinid 
                                             FROM provider_skin_mapping 
                                             WHERE provider_id = :providerId 
                                             AND provider_skinid = :providerSkinId");
        if (!$query->execute([
                ':providerId'     => $providerId,
                ':providerSkinId' => $skinId
            ]) || $query->rowCount() != 1
        ) {//if query fails or there is no returned rows from db
            throw new \SoapFault('DB_ERROR', 'Query failed.');
        } else {
            $result = $query->fetch(\PDO::FETCH_OBJ);
            $this->checkAndRegisterThirdPartyIntegrationUser($userId, (int)$result->poker_skinid, $soapClient);
        }
    }

    /**
     * @param int $userId
     * @param int $pokerSkinId
     * @param \SoapClient|null $soapClient
     * @param string|null $userDate
     * @return array
     * @throws \SoapFault
     */
    public function checkAndRegisterThirdPartyIntegrationUser(int $userId, int $pokerSkinId, \SoapClient $soapClient = null, string $userDate = null): array
    {
        $needUpdate = false;
        $isFirstLogin = 1;
        $returnData = [];
        if ($userDate != null) {//this whole if statement is executed only on com part
            $query = $this->db->prepare("SELECT ud.updatetime < :userDate AS isUpdated, c.user_id, 
                                                 (ud.logintime <= ud.acttime) AS isFirst 
                                                 FROM casino_ids c 
                                                 JOIN udata ud ON c.user_id = ud.uid 
                                                 WHERE c.provider_id = :providerId 
                                                 AND c.casino_id = :userId 
                                                 AND c.skin_id = :partnerId");
            $result = $query->execute([
                ':userDate'   => $userDate,
                ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                //only for com
                ':userId'     => $userId,
                ':partnerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId']
                //only for com
            ]);
            if ($result && $query->rowCount() == 1) {
                $result = $query->fetch(\PDO::FETCH_OBJ);
                $isFirstLogin = $result->isFirst ? 1 : 0;
                $needUpdate = $result->isUpdated || $isFirstLogin;
                $returnData = [
                    'status'   => 1,
                    'poker_id' => $result->user_id
                ];
            }
        } else {
            $query = $this->db->prepare("SELECT c.user_id, 
                                                 (ud.logintime <= ud.acttime) AS isFirst 
                                                 FROM casino_ids c 
                                                 JOIN udata ud ON c.user_id = ud.uid 
                                                 WHERE c.provider_id = :providerId 
                                                 AND c.casino_id = :userId 
                                                 AND c.skin_id = :partnerId");
            if (!$query->execute([
                ':userDate'   => $userDate,
                ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                //only for com
                ':userId'     => $userId,
                ':partnerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId']
                //only for com
            ])
            ) {
                //throw new \SoapFault('DB_ERROR', 'Query failed.');
            }
        }
        if ($query->rowCount() > 0 && $returnData['status'] === true) {
            $result = $query->fetch(\PDO::FETCH_OBJ);
            $isFirstLogin = $result->isFirst ? 1 : 0;
            $returnData = [
                'status'   => 1,
                'poker_id' => $result->user_id
            ];
        }
        if ($query->rowCount() == 0 || $needUpdate) {
            $legacyUserInfo = $this->soapClient->getUserInfo($userId, $pokerSkinId, $soapClient);
            /*if (is_soap_fault($legacyUserInfo) || $legacyUserInfo->UserGetInfoResult->resultCode != 1) {
                throw new \SoapFault('COMMUNICATION_ERROR', 'Error connecting to third party user endpoint.');
            }*/
            $user = $legacyUserInfo->UserGetInfoResult;//making variable shorter
            $params = [];
            $params['providerId'] = SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'];
            $params['userId'] = $userId;
            $params['skinId'] = SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId'];
            if (isset($user->agentId) && $user->agentId != 0) {
                $query = $this->db->prepare("SELECT * 
                                                     FROM provider_affil_mapping 
                                                     WHERE provider_id = :providerId 
                                                     AND provider_affilid = :agentId");
                $query->execute([
                    ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                    ':agentId'    => $user->agentId
                ]);
                if ($query->rowCount() != 1) {
                    $query = $this->db->prepare("INSERT INTO affiliates (name) 
                                                         VALUES (:agentName)");
                    if ($query->execute([
                        ':agentName' => $user->agentName
                    ])
                    ) {
                        $affiliateId = $this->db->lastInsertId();
                        $query = $this->db->prepare("INSERT INTO provider_affil_mapping (provider_id, provider_affilid, poker_affilid) 
                                                             VALUES (:providerId, :agentId, :affiliateId)");
                        $query->execute([
                            ':providerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
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