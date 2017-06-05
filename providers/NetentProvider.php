<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 19.05.2017
 * Time: 11:55
 */
declare(strict_types = 1);

namespace Providers;

use BackOffice\Bonus;
use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ConfigHelpers\Db;
use Helpers\SoapHelpers\NetentSoapClient;
use Users\UsersFactory;

class NetentProvider
{

    private $netentSoapClient;
    private $bonus;

    /**
     * NetentProvider constructor.
     * @param NetentSoapClient $netentSoapClient
     * @param Bonus $bonus
     */
    public function __construct(NetentSoapClient $netentSoapClient, Bonus $bonus)
    {
        $this->netentSoapClient = $netentSoapClient;
        $this->bonus = $bonus;
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
    public function login(array $thirdPartyServiceUser, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null): string
    {
        $netentSessionId = $this->netentSoapClient->loginUser($thirdPartyServiceUser);
        try {
            $sessionDetails = $this->createSession($thirdPartyServiceUser, $gameData, $amountInCents, $ip, $platform, $campaignId, $netentSessionId . ":" . $gameData['game_id']);
            if ($sessionDetails['returnCode'] != 1) {
                throw new \SoapFault((string)$sessionDetails['returnCode'], 'Return code from createSession method is not equal to 1.');
            }
        } catch (\SoapFault $soapFault) {
            if ($soapFault->faultcode == 101) {//specified error code that means not to stop execution but to logout and login again
                $this->netentSoapClient->logoutUser($netentSessionId);
                $this->login($thirdPartyServiceUser, $gameData, $amountInCents, $ip, $platform, $campaignId);
            } else {
                throw new \SoapFault($soapFault->faultcode, $soapFault->getMessage());
            }
        }
        return $netentSessionId;
    }

    /**
     * @param array $thirdPartyServiceUser
     * @param array $gameData
     * @param int $amountInCents
     * @param string|null $ip
     * @param int|null $platform
     * @param int|null $campaignId
     * @param string|null $netentSessionId
     * @return array
     * @throws \SoapFault
     */
    private function createSession(array $thirdPartyServiceUser, array $gameData, int $amountInCents, string $ip = null, int $platform = null, int $campaignId = null, string $netentSessionId = null): array
    {
        $returnValue = [];
        $user = UsersFactory::getUser($thirdPartyServiceUser, $gameData['provider_id']);
        /*if (isset($netentSessionId)) {
            $isCashierTokenSet = $user->getCashierTokenFromSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId']);//if there is cashier token already logout and login to netent again to obtain new cashier token because there can't be two same cashier tokens
            if ($isCashierTokenSet) {
                throw new \SoapFault('101', 'Cashier token already made, logout and login to obtain new cashier token.');
            }//uncomment this part when method is done because this part will exit the method because there is a cashierToken returned from function
        }*/
        if ((bool)ConfigManager::getIT('isItalian') === true) {
            $aamsGameCode = !!($thirdPartyServiceUser['sessionData']['option'] & 1) ? $gameData['aams_game_id_mobile'] : $gameData['aams_game_id_desktop'];
            if (empty($aamsGameCode)) {
                error_log('aamsGameCode not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                throw new \SoapFault('0', 'Unspecified error.');
            }
            if (empty($gameData['aams_game_type'])) {
                error_log('aamsGameType not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                throw new \SoapFault('0', 'Unspecified error.');
            } else {
                $aamsGameType = $gameData['aams_game_type'];
            }
            $doPGDACommunication = true;
        } else {
            $doPGDACommunication = false;
            $aamsGameCode = '';
            $aamsGameType = '';
        }
        $date = time();
        Db::getInstance(ConfigManager::getDb('database', true))->beginTransaction();
        try {
            $cashierToken = $user->getGameSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId'], $date);
            $qOne = "UPDATE thirdparty_sessions SET session_id = :sessionId WHERE id = :sessionId";
            $qTwo = "INSERT INTO tp_open_sessions (session_id, last_ping) VALUES (:sessionId, NOW())";
            $queryOne = Db::getInstance(ConfigManager::getDb('database', true))->prepare($qOne);
            $queryTwo = Db::getInstance(ConfigManager::getDb('database', true))->prepare($qTwo);
            if ($queryOne->execute([':sessionId' => $user->sessionId]) && $queryTwo->execute([':sessionId' => $user->sessionId])) {
                Db::getInstance(ConfigManager::getDb('database', true))->commit();
            }
        } catch (\SoapFault $soapFault) {
            Db::getInstance(ConfigManager::getDb('database', true))->rollBack();
            error_log("Updating and inserting of netend session ID failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
            throw new \SoapFault('0', 'Unspecified error.');
        }
        if (isset($amountInCents) && $amountInCents == 0 && (!isset($campaignId) || $campaignId == 0)) {
            return [
                'returnCode'          => ConfigManager::getIT('isItalian') ? 0 : 1,
                'sessionId'           => null,
                'amount'              => null,
                'cashierToken'        => $cashierToken,
                'aamsSessionId'       => null,
                'aamsParticipationId' => null
            ];
        }
        if (isset($gameData['is_slot'])) {
            $isSlot = $gameData['is_slot'];
        } else {
            if (empty($gameData['category_id'])) {
                error_log('Category ID not set in DB! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                throw new \SoapFault('0', 'Unspecified error.');
            } else {
                $isSlot = $gameData['category_id'] == 1;
            }
        }
        $user->externalSessionId = $user->sessionId;
        if (!isset($campaignId) || $campaignId == 0) {
            if (!$isSlot) {//change this, it goes without !
                $bonus = $user->getRealBonusAmount(true);
                $wagerCampaignDetails = $this->bonus->getWagerCampaignDetails();
                if (isset($wagerCampaignDetails['wagering_weekdays'])) {
                    $weekdays = explode(',', $wagerCampaignDetails['wagering_weekdays']);
                    if (in_array(date('N'), $weekdays)) {
                        $user->sendNotification(2);
                    }
                } else {
                    error_log("Wagering weekdays not set! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
                }
            } else {
                $bonus = null;
            }
            if (isset($bonus) && $bonus > 0) {
                if ($bonus >= $amountInCents) {
                    $bonus = $amountInCents;
                    $realAmount = 0;
                } else {
                    $realAmount = $amountInCents - $bonus;
                }
            } else {
                $bonus = 0;
                $realAmount = $amountInCents;
            }
        }

        $user->logSession(__FUNCTION__ . ": start: userid=" . $user->user['userid'] . " r_amount='$realAmount' bonus='$bonus' gameid='{$user->user['session_data']['gameid']}', aams_gamecode='$aamsGameCode', aams_gametype='$aamsGameType' ip='$ip' platform='$platform'");
        $returnValue['returnCode'] = 43;
        return $returnValue;//if return type is null script goes into endless loop
    }

}