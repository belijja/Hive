<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 15:32
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Configs\ThirdPartyIntegrationCodes;

class ThirdPartyIntegrationSoapClient implements ISoapClient
{
    private $tpiConfigs;

    public function __construct(ThirdPartyIntegrationCodes $tpiConfigs)
    {
        $this->tpiConfigs = $tpiConfigs;
    }

    public function getUserInfo(int $userId, int $pokerSkinId, \SoapClient $soapClient): /*array*/
    \stdClass
    {
        $response = new \stdClass();
        $response->UserGetInfoResult = new \stdClass();
        $response->UserGetInfoResult->_ResultCode = 1;
        $response->UserGetInfoResult->agentId = 11;
        $response->UserGetInfoResult->agentName = 'stanislav';
        $response->UserGetInfoResult->_ResultDescription = 'OK';
        $response->UserGetInfoResult->_AdditionalData = new \stdClass();
        $response->UserGetInfoResult->_Balance = new \stdClass();
        $response->UserGetInfoResult->_Balance->Amount = 32;
        $response->UserGetInfoResult->_Balance->AmountWithdrawable = 12;
        $response->UserGetInfoResult->_Balance->Currency = 'EUR';
        $response->UserGetInfoResult->_Balance->CuurencyID = 1;
        $response->UserGetInfoResult->_Credit = new \stdClass();
        $response->UserGetInfoResult->_Credit->Amount = 32;
        $response->UserGetInfoResult->_Credit->AmountWithdrawable = 12;
        $response->UserGetInfoResult->_Credit->Currency = 'EUR';
        $response->UserGetInfoResult->_Credit->CuurencyID = 1;
        $response->UserGetInfoResult->_Parameters = new \stdClass();
        $response->UserGetInfoResult = new \stdClass();
        $response->UserGetInfoResult->Address = 'Savska 10';
        $response->UserGetInfoResult->Birthdate = '19.05.1964';
        $response->UserGetInfoResult->City = 'Beograd';
        $response->UserGetInfoResult->country = 'dsfsd';
        $response->UserGetInfoResult->Currency = 1;
        $response->UserGetInfoResult->Email = 'dfsfs@sdasd.com';
        $response->UserGetInfoResult->FatherID = -1;
        $response->UserGetInfoResult->Firstname = 'Branislav';
        $response->UserGetInfoResult->Lastname = 'Jovic';
        $response->UserGetInfoResult->Phone = '323434242';
        $response->UserGetInfoResult->ProvinceResidenceCode = 'VOJ';
        $response->UserGetInfoResult->RegionResidenceCode = 'SAR';
        $response->UserGetInfoResult->Sex = 'M';
        $response->UserGetInfoResult->StateUser = -1;
        $response->UserGetInfoResult->TestUser = -1;
        $response->UserGetInfoResult->UserID = 32;
        $response->UserGetInfoResult->UserType = -1;
        $response->UserGetInfoResult->Username = 'belijja';
        $response->UserGetInfoResult->Zip = '324324';
        $response->UserGetInfoResult->_UserID = 32;
        $response->UserGetInfoResult->_FatherID = 3;
        return $response;
        /*$params['userId'] = $userId;
        if ($soapClient == null) {
            if (isset($this->tpiConfigs->getTpiConfigs($pokerSkinId)['wsdl'])) {
                $params = [
                    'trace'              => 1,
                    'exceptions'         => 0,
                    'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'connection_timeout' => $this->tpiConfigs->getTpiConfigs($pokerSkinId)['apiTimeout']
                ];
                if (isset($this->tpiConfigs->getTpiConfigs($pokerSkinId)['user'])) {
                    $params['login'] = $this->tpiConfigs->getTpiConfigs($pokerSkinId)['user'];
                    $params['password'] = $this->tpiConfigs->getTpiConfigs($pokerSkinId)['password'];
                }
                $soapClient = new \SoapClient($this->tpiConfigs->getTpiConfigs($pokerSkinId)['wsdl'], $params);
            } else {
                $soapClient = new SoapProxy($this->tpiConfigs->getTpiConfigs($pokerSkinId));
            }
        }
        return $soapClient->UserGetInfo($params);*/
    }

}