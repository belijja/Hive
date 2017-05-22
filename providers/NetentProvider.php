<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 19.05.2017
 * Time: 11:55
 */
declare(strict_types = 1);

namespace Providers;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\SoapHelpers\NetentSoapClient;
use Users\UsersFactory;

class NetentProvider
{

    private $soapClient;

    /**
     * NetentProvider constructor.
     * @param NetentSoapClient $soapClient
     */
    public function __construct(NetentSoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * @param array $user
     * @param array $gameData
     * @param int $amountInCents
     * @param string|null $ip
     * @param int|null $platform
     * @param int|null $campaignId
     * @return array
     * @throws \SoapFault
     */
    public function login(array $user, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null): array
    {
        $sessionId = $this->loginUser($user);
        if (is_soap_fault($sessionId)) {
            throw new \SoapFault('INTERNAL_ERROR', 'Error connecting to Netent server!');
        }
        $sessionDetails = $this->createSession($user, $gameData, $amountInCents, $ip, $platform, $campaignId, $sessionId . ":" . $gameData['game_id']);
        if ($sessionDetails['status'] == false) {
            if (is_soap_fault($this->logoutUser($sessionId))) {
                throw new \SoapFault('INTERNAL_ERROR', 'Error connecting to Netent server!');
            }
            $this->login($user, $gameData, $amountInCents, $ip, $platform, $campaignId);
        }
        if ($sessionDetails['returnCode'] != 1) {
            return $sessionDetails['returnCode'];
        } else {
            return $sessionId;
        }
    }

    private function createSession(array $user, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null, string $sessionId = null)
    {
        $user = UsersFactory::getUser($user['providerId']);
        if (isset($sessionId)) {
            $user->getTokenFromSession($sessionId, $user['sessionData']['gameId']);
        }
        return null;
    }

    /**
     * @param array $user
     * @return \Exception|mixed
     */
    private function loginUser(array $user): array
    {
        $returnFromNetent = [];
        try {
            $soapClient = $this->soapClient->getSoapClient();
            $userParams = [
                "userName"         => $user['userid'],
                "merchantId"       => ConfigManager::getNetent('merchantId'),
                "merchantPassword" => ConfigManager::getNetent('merchantPassword'),
                "currencyISOCode"  => $user['currency'],
                'extra'            => [
                    "DisplayName",
                    $user['username']
                ]
            ];
            $loginUserReturn = $soapClient->loginUserDetailed($userParams);
        } catch (\Exception $error) {
            error_log("Netent login failed! " . var_export($error, true));
            $returnFromNetent['status'] = $error;
            return $returnFromNetent;
        }
        $returnFromNetent = get_object_vars($loginUserReturn);
        /*return end($returnFromNetent);*/
        return ['loginUserDetailedReturn' => '1495447379571-1-S0B6WU2ZOK3S7'];
    }

}