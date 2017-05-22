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

    public function login(array $user)
    {
        $sessionId = $this->loginUser($user);
        if (is_soap_fault($sessionId)) {
            throw new \SoapFault('INTERNAL_ERROR', 'Error connecting to Netent server!');
        }
        $sessionDetails = $this->createSession();
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
            $userParams = ["userName"         => $user['userid'],
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
        return end($returnFromNetent);
    }

}