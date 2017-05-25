<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 25.05.2017
 * Time: 16:37
 */
declare(strict_types = 1);

namespace Configs;

use Helpers\ConfigHelpers\ConfigManager;

class ThirdPartyServicePartners
{
    private static $tpsConfigs = [
        'pw'         => [
            'password'   => 'pwpass',
            'providerId' => 2,
        ],
        'isibet'     => [
            'password'   => 'X5Plk4MB',
            'providerId' => 1001,
        ],
        'vintagames' => [
            'password'   => 'Pae7ial3',
            'providerId' => 1002,
        ],
        'misterbet'  => [
            'password'   => 'fuThohg6',
            'providerId' => 1003,
        ],
    ];

    public static function getTpsConfigs($partnerName)
    {
        return ConfigManager::checkIfArrayExists((string)$partnerName, self::$tpsConfigs);
    }
}