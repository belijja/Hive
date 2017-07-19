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
use Helpers\SoapHelpers\NetentSoapClient;
use Users\UserFactory;
use Pgda\PGDAIntegration;
use Containers\ServiceContainer;

class NetentProvider
{
    use ServiceContainer;

    private $netentSoapClient;
    private $bonus;
    private $pgda;

    /**
     * NetentProvider constructor.
     * @param NetentSoapClient $netentSoapClient
     * @param Bonus $bonus
     * @param PGDAIntegration $pgda
     */
    public function __construct(NetentSoapClient $netentSoapClient, Bonus $bonus, PGDAIntegration $pgda)
    {
        $this->netentSoapClient = $netentSoapClient;
        $this->bonus = $bonus;
        $this->pgda = $pgda;
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
        $user = UserFactory::getUser($thirdPartyServiceUser, $gameData['provider_id']);
        /*if (isset($netentSessionId)) {
            $isCashierTokenSet = $user->getCashierTokenFromSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId']);//if there is cashier token already logout and login to netent again to obtain new cashier token because there can't be two same cashier tokens
            if ($isCashierTokenSet) {
                throw new \SoapFault('101', 'Cashier token already made, logout and login to obtain new cashier token.');
            }//uncomment this part when method is done because this part will exit the method because there is a cashierToken returned from function
        }*/
        if ((bool)$this->container->get('Config')->getIT('isItalian') === true) {
            $aamsGameCode = !!($thirdPartyServiceUser['sessionData']['option'] & 1) ? $gameData['aams_game_id_mobile'] : $gameData['aams_game_id_desktop'];
            if (empty($aamsGameCode)) {
                $this->container->get('Logger')->log('error', true, 'aamsGameCode not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
                throw new \SoapFault('0', 'Unspecified error.');
            }
            if (empty($gameData['aams_game_type'])) {
                $this->container->get('Logger')->log('error', true, 'aamsGameType not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
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
        $this->container->get('Db')->getDb(true)->beginTransaction();
        try {
            $cashierToken = $user->getGameSession($netentSessionId, $thirdPartyServiceUser['sessionData']['gameId'], $date);
            $qOne = "UPDATE thirdparty_sessions SET session_id = :sessionId WHERE id = :sessionId";
            $qTwo = "INSERT INTO tp_open_sessions (session_id, last_ping) VALUES (:sessionId, NOW())";
            $queryOne = $this->container->get('Db')->getDb(true)->prepare($qOne);
            $queryTwo = $this->container->get('Db')->getDb(true)->prepare($qTwo);
            if ($queryOne->execute([':sessionId' => $user->sessionId]) && $queryTwo->execute([
                    ':sessionId' => $user->sessionId
                ])
            ) {
                $this->container->get('Db')->getDb(true)->commit();
            }
        } catch (\SoapFault $soapFault) {
            $this->container->get('Db')->getDb(true)->rollBack();
            $this->container->get('Logger')->log('error', true, "Updating and inserting of netend session ID failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
            throw new \SoapFault('0', 'Unspecified error.');
        }
        if (isset($amountInCents) && $amountInCents == 0 && (!isset($campaignId) || $campaignId == 0)) {
            return [
                'returnCode'          => $this->container->get('Config')->getIT('isItalian') ? 0 : 1,
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
            $this->container->get('Logger')->log('error', true, 'is_slot not set! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($gameData, true));
            throw new \SoapFault('0', 'Unspecified error.');
        }
        //insert session, state 0
        $user->externalSessionId = $user->sessionId;
        if (!isset($campaignId) || $campaignId == 0) {
            if ($isSlot) {
                $bonus = $user->getRealBonusAmount(true);
                $wagerCampaignDetails = $this->bonus->getWagerCampaignDetails();
                if (isset($wagerCampaignDetails['wagering_weekdays'])) {
                    $weekdays = explode(',', $wagerCampaignDetails['wagering_weekdays']);
                    if (in_array(date('N'), $weekdays)) {
                        $user->sendNotification(2);
                    }
                } else {
                    $this->container->get('Logger')->log('error', true, "Wagering weekdays not set! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
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
            $user->logSession(__FUNCTION__ . ": start: userId = " . $user->user['userid'] . ", realAmount = " . $realAmount . ", bonus = " . $bonus . ", gameId = " . $user->user['sessionData']['gameId'] . ", aamsGameCode = " . $aamsGameCode . ", aamsGameType = " . $aamsGameType . ", ip = " . $ip . ", platform = " . $platform);
            $query = $this->container->get('Db')->getDb(true)->prepare("INSERT INTO tp_ext_sessions (id, uid, state, amount, bonus_amount, ip, platform) VALUES (:id, :userId, 0, :amount, :bonusAmount, :ip, :platform)");
            if (/*!$query->execute([
                    ':id'          => $user->sessionId,
                    ':userId'      => $user->user['userid'],
                    ':amount'      => $amountInCents,
                    ':bonusAmount' => $bonus,
                    ':ip'          => $ip,
                    ':platform'    => $platform
                ]) || $query->rowCount() < 1*/
                1 == 2
            ) {
                $user->logSession(__FUNCTION__ . ": insert into tp_ext_sessions failed!");
                throw new \SoapFault('0', 'Unspecified error.');
            }
        } else {
            $user->logSession(__FUNCTION__ . ": start: userId = " . $user->user['userid'] . ", campaignId = " . $campaignId . ", gameId = " . $user->user['sessionData']['gameId'] . ", aamsGameCode = " . $aamsGameCode . ", aamsGameType = " . $aamsGameType . ", ip = " . $ip . ", platform = " . $platform);
            $query = $this->container->get('Db')->getDb(true)->prepare("INSERT INTO tp_ext_sessions (id, uid, state, ip, platform, campaign_id) VALUES (:id, :userId, 0, :ip, :platform, :campaignId)");
            if (!$query->execute([
                    ':id'         => $user->sessionId,
                    ':userId'     => $user->user['userid'],
                    ':ip'         => $ip,
                    ':platform'   => $platform,
                    ':campaignId' => $campaignId
                ]) || $query->rowCount() < 1
            ) {
                $user->logSession(__FUNCTION__ . ": insert into tp_ext_sessions failed!");
                throw new \SoapFault('0', 'Unspecified error.');
            }
        }
        //create PGDA session, state 1
        if ($doPGDACommunication) {
            $pgdaCode = $this->pgda->casinoCreate((int)$aamsGameCode, (int)$aamsGameType, $user->sessionId, date("Y-m-d H:i:s", $date), (isset($campaignId) && $campaignId != 0));
        }

        $returnValue['returnCode'] = 43;
        return $returnValue;//if return type is null script goes into endless loop
    }

}