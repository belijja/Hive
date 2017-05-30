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
use Helpers\ConfigHelpers\Db;
use Helpers\SoapHelpers\NetentSoapClient;
use Users\SKSUser;
use Users\UsersFactory;

class NetentProvider
{

    private $netentSoapClient;

    /**
     * NetentProvider constructor.
     * @param NetentSoapClient $netentSoapClient
     */
    public function __construct(NetentSoapClient $netentSoapClient)
    {
        $this->netentSoapClient = $netentSoapClient;
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
        $netentSessionId = $this->netentSoapClient->loginUser($thirdPartyServiceUser);//continue here
        if (is_soap_fault($netentSessionId) || array_key_exists('status', $netentSessionId)) {
            throw new \SoapFault('CONNECTION_ERROR', 'Error connecting to Netent server!');
        }
        $sessionDetails = $this->createSession($thirdPartyServiceUser, $gameData, $amountInCents, $ip, $platform, $campaignId, $netentSessionId . ":" . $gameData['game_id']);
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

    /**
     * @param array $thirdPartyServiceUser
     * @param array $gameData
     * @param int $amountInCents
     * @param string|null $ip
     * @param int|null $platform
     * @param int|null $campaignId
     * @param string|null $netentSessionId
     * @return array|null
     */
    private function createSession(array $thirdPartyServiceUser, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null, string $netentSessionId = null)
    {
        $returnValue = [];
        $user = UsersFactory::getUser($thirdPartyServiceUser, $gameData['provider_id']);
        if (is_null($user)) {
            error_log('Factory user not found! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($user, true));
            $returnValue['returnCode'] = -3;
            return $returnValue;
        }
        if (isset($netentSessionId)) {
            /*$cashierToken = $user->getCashierTokenFromSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId']);//if there is cashier token already logout and login to netent again to obtain new cashier token
            if (array_key_exists('cashiertoken', $cashierToken)) {
                $returnValue['status'] = false;
                return $returnValue;
            }*///un comment this part when method is done because this part will exit the method because there is a cashierToken returned from function
        }
        if ((bool)ConfigManager::getIT('isItalian') === true) {
            $aamsGameCode = !!($thirdPartyServiceUser['sessionData']['option'] & 1) ? $gameData['aams_game_id_mobile'] : $gameData['aams_game_id_desktop'];
            if (empty($aamsGameCode)) {
                error_log('aamsGameCode not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                $returnValue['returnCode'] = 0;
                return $returnValue;
            }
            if (empty($gameData['aams_game_type'])) {
                error_log('aamsGameType not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                $returnValue['returnCode'] = 0;
                return $returnValue;
            } else {
                $aamsGameType = $gameData['aams_game_type'];
            }
        } else {
            $isItalian = false;
            $aamsGameCode = '';
            $aamsGameType = '';
        }
        $date = time();
        Db::getInstance(ConfigManager::getDb('database', true))->beginTransaction();
        try {
            $gameSession = $user->getGameSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId'], $date);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return null;
    }

}