<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 24.05.2017
 * Time: 16:07
 */
declare(strict_types = 1);

namespace Users;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ConfigHelpers\Db;

class AbstractUsers
{
    protected $user;
    protected $config;

    protected $sessionId;
    protected $cashierToken;
    protected $active;
    protected $externalSessionId;
    protected $sessionStatus;
    protected $externalSessionState;
    protected $externalSessionCampaignId;
    protected $balance;
    protected $gameId;
    protected $gameName;
    protected $gameCode;

    /**
     * AbstractUsers constructor.
     * @param array $user
     * @param array $config
     */
    public function __construct(array $user, array $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * @param string $netentSessionId
     * @param int|null $gameId
     * @param bool|null $isActive
     * @return array
     */
    public function getCashierTokenFromSession(string $netentSessionId, int $gameId = null, bool $isActive = null): array
    {
        $q = "SELECT ts.id, ts.session_id, ts.seq, ts.cashiertoken, (p.active AND g.active) AS active, ts.status, 
              g.internal_game_id, g.aams_game_id_desktop, g.aams_game_id_mobile, g.game_name, 
              tes.id as ext_session_id, tes.state as ext_session_state, tes.campaign_id, tes.amount as balance 
              FROM thirdparty_sessions ts 
              LEFT JOIN tp_ext_sessions tes ON ts.session_id = tes.id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_provider p ON ts.thirdparty_provider_id = p.provider_id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_game g ON ts.game_id = g.internal_game_id 
              WHERE ts.thirdparty_provider_id = :providerId
              AND ts.thirdparty_session_id = :netentSessionId 
              AND ts.uid = :userId";
        if (isset($gameId)) {
            $q .= " AND ts.game_id = :gameId";
        }
        $q .= " ORDER BY ts.seq DESC LIMIT 1";
        $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare($q);
        $query->bindParam(':providerId', $this->config['providerId']);
        $query->bindParam(':netentSessionId', $netentSessionId);
        $query->bindParam(':userId', $this->user['userid']);
        if (isset($gameId)) {
            $query->bindParam(':gameId', $gameId);
        }
        if ($query->execute() && $query->rowCount() > 0) {
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            $this->sessionId = $result['id'];
            $this->cashierToken = $result['cashiertoken'];
            $this->active = $result['active'];
            $this->externalSessionId = $result['ext_session_id'];
            $this->sessionStatus = $result['status'];
            $this->externalSessionState = $result['ext_session_state'];
            $this->externalSessionCampaignId = $result['campaign_id'];
            $this->balance = $result['balance'];
            $this->gameId = $result['internal_game_id'];
            $this->gameName = $result['game_name'];
            if (substr($result['cashiertoken'], 0, 1) == "X") {
                $this->gameCode = $result['aams_game_id_desktop'];
            } else if (substr($result['cashiertoken'], 0, 1) == "M") {
                $this->gameCode = $result['aams_game_id_mobile'];
            }
            if (isset($this->externalSessionState) && $this->externalSessionState < 2) {
                $result['returnValue'] = false;
            }
            if ($isActive && !$this->newTransactionOk()) {
                $result['returnValue'] = false;
            }
            return $result;
        } else {
            $result['returnValue'] = false;
        }
        return $result;
    }

    /**
     * @return bool
     */
    private function newTransactionOk(): bool
    {
        return (!isset($this->active) || $this->active) && $this->userNewTransactionOk();
    }

    /**
     * @param string|null $netentSessionId
     * @param int $gameId
     * @param int|null $date
     * @return null
     */
    public function getGameSession(string $netentSessionId = null, int $gameId, int $date = null)
    {
        if (!isset($date)) {
            $date = time();
        }
        return $this->insertThirdPartySession(0, $netentSessionId, 1, $gameId, $this->getNewCashierToken(), 0, 0, 0, 0, $date, $date);
    }

    /**
     * @param string|null $oldCashierToken
     * @param int $max
     * @return string
     */
    public function getNewCashierToken(string $oldCashierToken = null, int $max = 5)
    {
        $cashierToken = '';
        if (isset($oldCashierToken)) {
            $cashierToken = $this->isCashierTokenTyped($oldCashierToken) ? $oldCashierToken : '';
        } else {
            if (is_array($this->user['sessionData'])) {
                if ($this->user['sessionData']['option'] & 1) {
                    $cashierToken = 'M';
                } else {
                    $cashierToken = 'X';
                }
            } else {
                error_log('Missing session data! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($this->user, true));
            }
        }
        for ($i = 0; $i < $max; $i++) {
            $stringOfBytes = openssl_random_pseudo_bytes(4);
            $cashierToken .= bin2hex($stringOfBytes);
        }
        return $cashierToken;
    }

    /**
     * @param string $oldCashierToken
     * @return bool
     */
    private function isCashierTokenTyped(string $oldCashierToken)
    {
        return $oldCashierToken[0] == 'M' || $oldCashierToken[0] == 'X';
    }

    /**
     * @param int $status
     * @param string $netentSessionId
     * @param int $sequence
     * @param int $gameId
     * @param string $cashierToken
     * @param int $numberOfHands
     * @param int $amount
     * @param int $bet
     * @param int $rake
     * @param int $startDate
     * @param int $endDate
     * @param string|null $sessionId
     * @return string
     */
    public function insertThirdPartySession(int $status, string $netentSessionId, int $sequence, int $gameId, string $cashierToken, int $numberOfHands, int $amount, int $bet, int $rake, int $startDate, int $endDate, string $sessionId = null): string
    {
        $newCashierToken = false;

        if (!isset($netentSessionId)) {
            $netentSessionId = hash("sha256", $cashierToken);
        }
        $q = "INSERT INTO thirdparty_sessions (session_id, status, uid, thirdparty_provider_id, game_id, seq, thirdparty_session_id, cashiertoken, nrhands, amount, bet, rake, sessionstart, sessionend) 
              VALUES (:sessionId, :status, :userId, :thirdPartyProviderId, :gameId, :sequence, :thirdPartySessionId, :cashierToken, :numberOfHands, :amount, :bet, :rake, FROM_UNIXTIME(:sessionStart), FROM_UNIXTIME(:sessionEnd))";
        $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare($q);
        if (!$query->execute([
                ':sessionId'            => $sessionId,
                ':status'               => $status,
                ':userId'               => $this->user['userid'],
                ':thirdPartyProviderId' => $this->config['providerId'],
                ':gameId'               => $gameId,
                ':sequence'             => $sequence,
                ':thirdPartySessionId'  => $netentSessionId,
                //this variable comes from netent side and that is why it goes in thirdparty_session_id column
                ':cashierToken'         => $cashierToken,
                ':numberOfHands'        => $numberOfHands,
                ':amount'               => $amount,
                ':bet'                  => $bet,
                ':rake'                 => $rake,
                ':sessionStart'         => $startDate,
                ':sessionEnd'           => $endDate
            ]) || $query->rowCount() != 1
        ) {//there will be no insertion because of unique indexed columns in db
            $q = "SELECT id,cashiertoken FROM thirdparty_sessions 
                  WHERE uid = :userId 
                  AND thirdparty_provider_id = :providerId 
                  AND seq = :sequence
                  AND thirdparty_session_id = :sessionId" . ($sequence == 1 ? " AND nrhands = :numberOfHands AND bet = :bet AND rake = :rake" : "");
            $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare($q);
            $query->bindParam(':userId', $this->user['userid']);
            $query->bindParam(':providerId', $this->config['providerId']);
            $query->bindParam(':sequence', $sequence);
            $query->bindParam(':sessionId', $netentSessionId);
            if ($sequence == 1) {
                $query->bindParam(':numberOfHands', $numberOfHands);
                $query->bindParam(':bet', $bet);
                $query->bindParam(':rake', $rake);
            }
            if ($query->execute() && $query->rowCount() > 1) {
                $result = $query->fetch(\PDO::FETCH_ASSOC);//fetching data from thirdparty_sessions if new session can't be added
                $this->sessionId = $result['id'];
                $newCashierToken = $result['cashiertoken'];
                if ($sequence > 1) {
                    $newCashierToken = $this->updateThirdPartySession($newCashierToken, $numberOfHands, $amount, $bet, $rake);
                }
            } else {
                $this->sessionId = Db::getInstance(ConfigManager::getDb('database', true))->lastInsertId();
                $newCashierToken = $cashierToken;
            }
        }
        return $newCashierToken;
    }
}