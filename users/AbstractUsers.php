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
    private $user;
    private $config;

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

    public function getTokenFromSession($sessionId, $gameId = null, $isActive = null)
    {
        $q = "SELECT ts.id, ts.session_id, ts.seq, ts.cashiertoken, (p.active AND g.active) AS active, ts.status, 
              g.internal_game_id, g.aams_game_id_desktop, g.aams_game_id_mobile, g.game_name, 
              tes.id as ext_session_id, tes.state as ext_session_state, tes.campaign_id, tes.amount as balance 
              FROM thirdparty_sessions ts 
              LEFT JOIN tp_ext_sessions tes ON ts.session_id=tes.id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_provider p ON ts.thirdparty_provider_id=p.provider_id 
              LEFT JOIN " . ConfigManager::getDb('database', false) . ".hg_game g ON ts.game_id=g.internal_game_id 
              WHERE ts.thirdparty_provider_id = :providerId
              AND ts.thirdparty_session_id = :sessionId 
              AND ts.uid = :userId";
        Db::getInstance(ConfigManager::getDb('database', true))->prepare($q);
        //continue here
    }
}