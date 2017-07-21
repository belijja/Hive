<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 15:26
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Containers\ServiceContainer;

class SKSSoapClient implements ISoapClient
{
    use ServiceContainer;

    /**
     * @param int $userId
     * @param int $skinId
     * @param \SoapClient|null $soapClient
     * @return \stdClass
     */
    public function getUserInfo(int $userId, int $skinId, \SoapClient $soapClient = null): /*array*/
    \stdClass
    {
        $response = new \stdClass();
        $response->GetUserInfoResult = new \stdClass();
        $response->GetUserInfoResult->_ResultCode = 6482334;
        $response->GetUserInfoResult->_ResultDescription = 'OK';
        $response->GetUserInfoResult->_AdditionalData = new \stdClass();
        $response->GetUserInfoResult->_Balance = new \stdClass();
        $response->GetUserInfoResult->_Balance->Amount = 32;
        $response->GetUserInfoResult->_Balance->AmountWithdrawable = 12;
        $response->GetUserInfoResult->_Balance->Currency = 'EUR';
        $response->GetUserInfoResult->_Balance->CuurencyID = 1;
        $response->GetUserInfoResult->_Credit = new \stdClass();
        $response->GetUserInfoResult->_Credit->Amount = 32;
        $response->GetUserInfoResult->_Credit->AmountWithdrawable = 12;
        $response->GetUserInfoResult->_Credit->Currency = 'EUR';
        $response->GetUserInfoResult->_Credit->CuurencyID = 1;
        $response->GetUserInfoResult->_Parameters = new \stdClass();
        $response->GetUserInfoResult->_UserInfo = new \stdClass();
        $response->GetUserInfoResult->_UserInfo->Address = 'Savska 10';
        $response->GetUserInfoResult->_UserInfo->Birthdate = '19.05.1964';
        $response->GetUserInfoResult->_UserInfo->City = 'Beograd';
        $response->GetUserInfoResult->_UserInfo->Country = '4';
        $response->GetUserInfoResult->_UserInfo->Currency = 1;
        $response->GetUserInfoResult->_UserInfo->Email = 'dfsfs@sdasd.com';
        $response->GetUserInfoResult->_UserInfo->FatherID = -1;
        $response->GetUserInfoResult->_UserInfo->Firstname = 'Branislav';
        $response->GetUserInfoResult->_UserInfo->Lastname = 'Jovic';
        $response->GetUserInfoResult->_UserInfo->Phone = '323434242';
        $response->GetUserInfoResult->_UserInfo->ProvinceResidenceCode = 'VOJ';
        $response->GetUserInfoResult->_UserInfo->RegionResidenceCode = 'SAR';
        $response->GetUserInfoResult->_UserInfo->Sex = 'M';
        $response->GetUserInfoResult->_UserInfo->StateUser = -1;
        $response->GetUserInfoResult->_UserInfo->TestUser = -1;
        $response->GetUserInfoResult->_UserInfo->UserID = 32;
        $response->GetUserInfoResult->_UserInfo->UserType = -1;
        $response->GetUserInfoResult->_UserInfo->Username = 'belijja';
        $response->GetUserInfoResult->_UserInfo->Zip = '324324';
        $response->GetUserInfoResult->_UserID = 32;
        $response->GetUserInfoResult->_FatherID = 3;
        return $response;
        /*$params = $this->initSKSParams($skinId);
        $params['_UserID'] = $userId;
        if ($soapClient == null) {
            $soapClient = $this->getSKSSoapClient('Users');
        }
        $response = $soapClient->GetUserInfo(['objRequest' => $params]);
        if (!is_soap_fault($response) && isset($response->GetUserInfoResult->_UserInfo->UserID) && !isset($response->GetUserInfoResult->_UserID)) {
            $response->GetUserInfoResult->_UserID = (int)$response->GetUserInfoResult->_UserInfo->UserID;
            $response->GetUserInfoResult->_FatherID = (int)$response->GetUserInfoResult->_UserInfo->FatherID;
        } else {
            $this->container->get('Logger')->log('error', true, 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ .  ' VARIABLE: ' . var_export($params, true));
        }
        return $response;
    }

    private function getSKSSoapClient(string $service = 'Users'): \SoapClient
    {
        return new \SoapClient($this->container->get('Config')->getSKS('apiUri') . "/" . $service . ".svc?wsdl", [
            'trace'              => 1,
            'exceptions'         => 0,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'connection_timeout' => $this->container->get('Config')->getSKS('apiConnectionTimeout')
        ]);
    }

    private function initSKSParams(int $skinId, string $apiAccount = null, string $apiPass = null): array
    {
        if (!isset($apiAccount)) {
            $apiAccount = $this->container->get('Config')->getSKS('apiAccount');
        }
        if (!isset($apiPass)) {
            $apiPass = $this->container->get('Config')->getSKS('apiPassword');
        }
        $params = [
            '_APIAccount'  => $apiAccount,
            '_APIPassword' => $apiPass,
            '_IDBookmaker' => $skinId
        ];
        return $params;*/
    }

}