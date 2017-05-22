<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 22.05.2017
 * Time: 14:33
 */
declare(strict_types = 1);

namespace Users;

use Configs\ThirdPartyGamesConfigs;
use Helpers\ConfigHelpers\ConfigManager;

class UsersFactory
{

    private static $tpiConfigs;

    public function __construct(ThirdPartyGamesConfigs $tpiConfigs)
    {
        self::$tpiConfigs = $tpiConfigs;
    }

    public static function getUser($providerId)
    {
        $user = null;
        foreach (self::$tpiConfigs as $key => $value) {
            if ($providerId == $value['providerId']) {
                $handlerName = ConfigManager::getHandler($providerId);
                $user = new $handlerName();
            }
        }
        return $user;
    }

}