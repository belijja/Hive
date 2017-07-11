<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 22.05.2017
 * Time: 10:32
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\LogHelpers\LogManager;

class NetentSoapClient
{
    /**
     * @param array $user
     * @return string
     * @throws \SoapFault
     */
    public function loginUser(array $user): string
    {
        /*try {
            $soapClient = $this->getSoapClient();
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
            LogManager::log('error', true, "Netent login failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
            throw new \SoapFault('CONNECTION_ERROR', 'Error connecting to Netent server!');
        }
        $returnFromNetent = get_object_vars($loginUserReturn);
        return end($returnFromNetent);*/
        $returnValue = '1495447379571-1-S0B6WU2ZOK3S7';
        return $returnValue;
    }

    /**
     * @param $netentSessionId
     * @throws \SoapFault
     * @return void
     */
    public function logoutUser($netentSessionId): void
    {
        /*try {
            $soapClient = $this->getSoapClient();
            $params = [
                'sessionId' => $netentSessionId,
                'merchantId'       => ConfigManager::getNetent('merchantId'),
                'merchantPassword' => ConfigManager::getNetent('merchantPassword')
            ];
            $soapClient->logoutUser($params);
        } catch (\Exception $error) {
            LogManager::log('error', true, "Netent logout failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
            throw new \SoapFault('CONNECTION_ERROR', 'Error connecting to Netent server!');
        }*/
        return;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient()
    {
        return new \SoapClient(ConfigManager::getNetent('apiLocation'), [
            'trace'              => 1,
            'connection_timeout' => ConfigManager::getNetent('apiConnectionTimeout')
        ]);
    }
}