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
    public static function getUser($gameProviderId, $userProviderId)
    {
        $user = null;
        foreach (ThirdPartyGamesConfigs::getTpgConfigs() as $key => $value) {
            if ($gameProviderId == $value['providerId']) {
                $handlerName = ConfigManager::getHandler($userProviderId);
                $user = new $handlerName();
            }
        }
        return $user;
    }

}