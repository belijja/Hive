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
     * @param array $thirdPartyServiceUser
     * @param array $gameData
     * @param int $amountInCents
     * @param string|null $ip
     * @param int|null $platform
     * @param int|null $campaignId
     * @return string
     * @throws \SoapFault
     */
    public function login(array $thirdPartyServiceUser, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null)
    {
        $sessionId = $this->loginUser($thirdPartyServiceUser);
        if (is_soap_fault($sessionId)) {
            throw new \SoapFault('INTERNAL_ERROR', 'Error connecting to Netent server!');
        }
        $sessionDetails = $this->createSession($thirdPartyServiceUser, $gameData, $amountInCents, $ip, $platform, $campaignId, $sessionId . ":" . $gameData['game_id']);
        /*if ($sessionDetails['status'] == false) {
            if (is_soap_fault($this->logoutUser($sessionId))) {
                throw new \SoapFault('INTERNAL_ERROR', 'Error connecting to Netent server!');
            }
            $this->login($thirdPartyServiceUser, $gameData, $amountInCents, $ip, $platform, $campaignId);
        }
        if ($sessionDetails['returnCode'] != 1) {
            return null;//change return values
        } else {
            return null;//change return values
        }*/
    }

    private function createSession(array $thirdPartyServiceUser, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null, string $sessionId = null)
    {
        $returnValue = [];
        $user = UsersFactory::getUser($thirdPartyServiceUser, $gameData['provider_id']);
        if (is_null($user)) {
            error_log('Factory user not found! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($user, true));
            $returnValue['returnCode'] = -3;
            return $returnValue;
        }
        if (isset($sessionId)) {
            $cashierToken = $user->getCashierTokenFromSession($sessionId, $thirdPartyServiceUser['sessionData']['gameId']);
            if (array_key_exists('cashiertoken', $cashierToken)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
        }
        return null;
    }

    /**
     * @param array $user
     * @return string
     */
    private function loginUser(array $user): string
    {
        /*$returnFromNetent = [];
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
            error_log("Netent login failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
            $returnFromNetent['status'] = $error;
            return $returnFromNetent;
        }
        $returnFromNetent = get_object_vars($loginUserReturn);
        return end($returnFromNetent);*/
        $returnValue = ['loginUserDetailedReturn' => '1495447379571-1-S0B6WU2ZOK3S7'];
        return end($returnValue);
    }

}