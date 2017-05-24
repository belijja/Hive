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

class UsersFactory
{
    public static function getUser(array $thirdPartyServiceUser, string $gameProviderId)
    {
        $user = null;
        foreach (ThirdPartyGamesConfigs::getTpgConfigs() as $key => $value) {
            if ($gameProviderId == $value['providerId']) {
                if (is_null($user)) {
                    $user = $thirdPartyServiceUser['provider_id'] == 2 ? new SKSUser($thirdPartyServiceUser, $value) : new TPUser($thirdPartyServiceUser, $value);
                }
            }
        }
        return $user;
    }

}