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

    public function getCashierTokenFromSession(string $sessionId, int $gameId = null, bool $isActive = null): array
    {
        $q = "SELECT ts.id, ts.session_id, ts.seq, ts.cashiertoken, (p.active AND g.active) AS active, ts.status, 
              g.internal_game_id, g.aams_game_id_desktop, g.aams_game_id_mobile, g.game_name, 
              tes.id as ext_session_id, tes.state as ext_session_state, tes.campaign_id, tes.amount as balance 
              FROM thirdparty_sessions ts 
              LEFT JOIN tp_ext_sessions tes ON ts.session_id = tes.id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_provider p ON ts.thirdparty_provider_id = p.provider_id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_game g ON ts.game_id = g.internal_game_id 
              WHERE ts.thirdparty_provider_id = :providerId
              AND ts.thirdparty_session_id = :sessionId 
              AND ts.uid = :userId";
        if (isset($gameId)) {
            $q .= " AND ts.game_id = :gameId";
        }
        $q .= " ORDER BY ts.seq DESC LIMIT 1";
        $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare($q);
        $query->bindParam(':providerId', $this->config['providerId']);
        $query->bindParam(':sessionId', $sessionId);
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
                $result['status'] = false;
            }
            if ($isActive && !$this->newTransactionOk()) {
                $result['status'] = false;
            }
            return $result['cashiertoken'];
        } else {
            $result['status'] = false;
        }
        return $result;
    }

    private function newTransactionOk(): bool
    {
        return (!isset($this->active) || $this->active) && $this->userNewTransactionOk();
    }
}