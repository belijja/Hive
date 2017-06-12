<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 13:43
 */
declare(strict_types = 1);

namespace Configs;

use Helpers\ConfigHelpers\ConfigManager;

/**
 * Class SkinConfigs
 * @package Configs
 */
class SkinConfigs
{

    private static $skinConfigs = [
        0        => [
            'urlLanguages'     => [
                0  => 'en',
                1  => 'ro',
                2  => 'hu',
                3  => 'de',
                4  => 'ru',
                5  => 'cs',
                6  => 'es',
                7  => 'it',
                8  => 'bs',
                9  => 'hr',
                10 => 'sr',
                11 => 'sr',
                12 => 'zh'
            ],
            'urlDestinations'  => [
                1 => 'profile',
                2 => 'transfer',
                3 => 'forgotpass',
                4 => 'deposit',
                5 => 'cashout',
                6 => 'registration',
                7 => 'limits'
            ],
            'providerMappings' => [
                4 => 1
            ]
        ],
        4194305  => [
            'aamsConc' => 15242
        ],
        12582913 => [
            'postUrl'    => 'https://stage-wallet.yosware.it/hive_rest/',
            'apiTimeout' => 5,
            'providerId' => 1002,
            'partnerId'  => 1,
        ]
    ];

    /**
     * @param int $skinId
     * @return array
     */
    public static function getSkinConfigs(int $skinId): array
    {
        return ConfigManager::checkIfArrayExists((string)$skinId, self::$skinConfigs);
    }

}