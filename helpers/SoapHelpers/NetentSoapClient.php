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

class NetentSoapClient
{
    /**
     * @param array $user
     * @return array
     */
    public function loginUser(array $user): array
    {
        /*$returnFromNetent = [];
        try {
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
            error_log("Netent login failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
            $returnFromNetent['status'] = $error;
            return $returnFromNetent;
        }
        $returnFromNetent = get_object_vars($loginUserReturn);
        return $returnFromNetent;*/
        $returnValue = ['loginUserDetailedReturn' => '1495447379571-1-S0B6WU2ZOK3S7'];
        return $returnValue;
    }

    public function getSoapClient()
    {
        return new \SoapClient(ConfigManager::getNetent('apiLocation'), [
            'trace'              => 1,
            'connection_timeout' => ConfigManager::getNetent('apiConnectionTimeout')
        ]);
    }
}