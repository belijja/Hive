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
        $sessionId = $this->netentSoapClient->loginUser($thirdPartyServiceUser);
        if (is_soap_fault($sessionId) || array_key_exists('status', $sessionId)) {
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
            $cashierToken = $user->getCashierTokenFromSession($sessionId, $thirdPartyServiceUser['sessionData']['gameId']);//if there is cashier token already logout and login to netent again to obtain new cashier token
            if (array_key_exists('cashiertoken', $cashierToken)) {
                $returnValue['status'] = false;
                return $returnValue;
            }
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
        $gameSession = $user->getGameSession($sessionId, $thirdPartyServiceUser['sessionData']['gameId'], $date);
        return null;
    }

}