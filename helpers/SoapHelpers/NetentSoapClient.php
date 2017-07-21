<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 22.05.2017
 * Time: 10:32
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Containers\ServiceContainer;

class NetentSoapClient
{
    use ServiceContainer;

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
                "merchantId"       => $this->container->get('Config')->getNetent('merchantId'),
                "merchantPassword" => $this->container->get('Config')->getNetent('merchantPassword'),
                "currencyISOCode"  => $user['currency'],
                'extra'            => [
                    "DisplayName",
                    $user['username']
                ]
            ];
            $loginUserReturn = $soapClient->loginUserDetailed($userParams);
        } catch (\Exception $error) {
            $this->container->get('Logger')->log('error', true, "Netent login failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
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
                'merchantId'       => $this->container->get('Config')->getNetent('merchantId'),
                'merchantPassword' => $this->container->get('Config')->getNetent('merchantPassword')
            ];
            $soapClient->logoutUser($params);
        } catch (\Exception $error) {
            $this->container->get('Logger')->log('error', true, "Netent logout failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($error, true));
            throw new \SoapFault('CONNECTION_ERROR', 'Error connecting to Netent server!');
        }*/
        return;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient()
    {
        return new \SoapClient($this->container->get('Config')->getNetent('apiLocation'), [
            'trace'              => 1,
            'connection_timeout' => $this->container->get('Config')->getNetent('apiConnectionTimeout')
        ]);
    }
}