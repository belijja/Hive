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
use Helpers\LogHelpers\LogManager;
use Helpers\ConfigHelpers\Db;
use Services\AbstractContainer;

/**
 * Class ThirdPartyIntegrationPartner
 * @package Partners
 */
class ThirdPartyIntegrationPartner extends AbstractContainer implements IPartner
{
    private $soapClient;
    private $db;
    private $logger;
    private $serverManager;

    public function __construct(ThirdPartyIntegrationSoapClient $soapClient, ServerManager $serverManager)
    {
        parent::__construct();
        $this->soapClient = $soapClient;
        $this->serverManager = $serverManager;
        $this->db = $this->container->get('Db');
        $this->logger = $this->container->get('Logger');
    }

    /**
     * @param int $userId
     * @param int $skinId
     * @param int $partnerId
     * @param \SoapClient|null $soapClient
     * @throws \SoapFault
     * @return void
     */
    public function checkAndRegisterUser(int $userId, int $skinId, int $partnerId, \SoapClient $soapClient = null): void
    {
        $query = $this->db->getDb(true)->prepare("SELECT poker_skinid 
                                             FROM provider_skin_mapping 
                                             WHERE provider_id = :providerId 
                                             AND provider_skinid = :providerSkinId");
        if (!$query->execute([
                ':providerId'     => $partnerId,
                ':providerSkinId' => $skinId
            ]) || $query->rowCount() != 1
        ) {//if query fails or there is no returned rows from db
            throw new \SoapFault('-3', 'Query failed.');
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
        $result['authorityId'] = null;//added because of the -variable might have not be defined- notice
        $needUpdate = false;
        $isFirstLogin = 1;
        $returnData = [];
        if ($userDate != null) {//this whole if statement is executed only on com part
            $query = $this->db->getDb(true)->prepare("SELECT ud.updatetime < :userDate AS isUpdated, c.user_id, 
                                                 (ud.logintime <= ud.acttime) AS isFirst 
                                                 FROM casino_ids c 
                                                 JOIN udata ud ON c.user_id = ud.uid 
                                                 WHERE c.provider_id = :providerId 
                                                 AND c.casino_id = :userId 
                                                 AND c.skin_id = :partnerId");
            $result = $query->execute([
                ':userDate'   => $userDate,
                ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                ':userId'     => $userId,
                ':partnerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId']
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
            $query = $this->db->getDb(true)->prepare("SELECT c.user_id, 
                                                 (ud.logintime <= ud.acttime) AS isFirst,
                                                 teud.authority_id as authorityId
                                                 FROM casino_ids c 
                                                 JOIN udata ud ON c.user_id = ud.uid
                                                 LEFT JOIN tp_ext_userdata teud ON c.user_id = teud.casino_id 
                                                 WHERE c.provider_id = :providerId 
                                                 AND c.casino_id = :userId 
                                                 AND c.skin_id = :partnerId");
            if (!$query->execute([
                ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                ':userId'     => $userId,
                ':partnerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId']
            ])
            ) {
                throw new \SoapFault('-3', 'Query failed.');
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
            $legacyUserInfo = $this->soapClient->getUserInfo($userId, $pokerSkinId, $this->logger, $soapClient);
            /*if (is_soap_fault($legacyUserInfo) || $legacyUserInfo->UserGetInfoResult->resultCode != 1) {
                throw new \SoapFault('-3', 'Error connecting to third party user endpoint.');
            }*/
            $user = $legacyUserInfo->UserGetInfoResult;//making variable shorter
            $params = [];
            $params['providerId'] = SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'];
            $params['userId'] = $userId;
            $params['skinId'] = SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId'];
            if (isset($user->agentId) && $user->agentId != 0) {
                $query = $this->db->getDb(true)->prepare("SELECT * 
                                                     FROM provider_affil_mapping 
                                                     WHERE provider_id = :providerId 
                                                     AND provider_affilid = :agentId");
                $query->execute([
                    ':providerId' => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                    ':agentId'    => $user->agentId
                ]);
                if ($query->rowCount() != 1) {
                    $query = $this->db->getDb(true)->prepare("INSERT INTO affiliates (name) 
                                                         VALUES (:agentName)");
                    if ($query->execute([
                        ':agentName' => $user->agentName
                    ])
                    ) {
                        $affiliateId = $this->db->lastInsertId();
                        $query = $this->db->getDb(true)->prepare("INSERT INTO provider_affil_mapping (provider_id, provider_affilid, poker_affilid) 
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
                if (!empty($user->authorityId) && is_null($result['authorityId'])) {
                    $query = $this->db->getDb(true)->prepare("INSERT INTO tp_ext_userdata (provider_id, casino_id, skin_id, authority_id) VALUES (:providerId, :userId, :partnerId, :authorityId)");
                    if ($query->execute([
                        ':providerId'  => SkinConfigs::getSkinConfigs($pokerSkinId)['providerId'],
                        ':userId'      => $userId,
                        ':partnerId'   => SkinConfigs::getSkinConfigs($pokerSkinId)['partnerId'],
                        ':authorityId' => $user->authorityId
                    ])
                    ) {
                        $params['authorityId'] = $user->authorityId;
                    }
                }
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