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
 * Class ThirdPartyIntegrationConfigs
 * @package Configs
 */
class ThirdPartyIntegrationConfigs
{

    private static $tpiConfigs = [
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
        8388609  => [
            'postUrl'    => 'http://localhost/backend_qa/soap_post_test.php',
            'apiKey'     => '6b0932eb95355dbeb3f44325dba9d97444a6c516666d615d3c9ffaff29cb03f2',
            'apiTimeout' => 5,
            'providerId' => 7,
            'partnerId'  => 1,
        ],
        16777217 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 1,
        ],
        16777218 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 4,
        ],
        16777219 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 14,
        ],
        16777220 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 7,
        ],
        16777221 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 6,
        ],
        16777222 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 16,
        ],
        16777223 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 21,
        ],
        16777224 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 22,
        ],
        16777225 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 23,
        ],
        16777226 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 30,
        ],
        16777227 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 31,
        ],
        16777228 => [
            'wsdl'       => 'http://services.cardclubgames.com/hive.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 5,
            'partnerId'  => 32,
        ],
        20971521 => [
            'wsdl'       => 'http://mani.pronetdev.com/HivePokerService?wsdl',
            'apiTimeout' => 5,
            'providerId' => 6,
            'partnerId'  => 6,
        ],
        20971522 => [
            'wsdl'       => 'http://mani.pronetdev.com/HivePokerService?wsdl',
            'apiTimeout' => 5,
            'providerId' => 6,
            'partnerId'  => 7,
        ],
        20971523 => [
            'wsdl'       => 'http://mani.pronetdev.com/HivePokerService?wsdl',
            'apiTimeout' => 5,
            'providerId' => 6,
            'partnerId'  => 8,
        ],
        20971524 => [
            'wsdl'       => 'http://mani.pronetdev.com/HivePokerService?wsdl',
            'apiTimeout' => 5,
            'providerId' => 6,
            'partnerId'  => 9,
        ],
        25165825 => [
            'wsdl'       => 'http://external-services-centurionbet-staging.3bg.biz/Integrations/HivePoker/HivePokerExternalService.svc?wsdl',
            'apiTimeout' => 5,
            'providerId' => 8,
            'partnerId'  => 1000,
        ],
        25165826 => [
            'wsdl'       => 'http://external-services-avery-staging.3bg.biz/Integrations/HivePoker/HivePokerExternalService.svc?wsdl',
            'apiTimeout' => 5,
            'providerId' => 8,
            'partnerId'  => 1100,
        ],
        25165827 => [
            'wsdl'       => 'http://external-services-centurionbet-staging.3bg.biz/Integrations/HiveGames/HiveGamesExternalService.svc?wsdl',
            'apiTimeout' => 5,
            'providerId' => 8,
            'partnerId'  => 10000,
        ],
        25165828 => [
            'wsdl'       => 'http://external-services-avery-staging.3bg.biz/Integrations/HiveGames/HiveGamesExternalService.svc?wsdl',
            'apiTimeout' => 5,
            'providerId' => 8,
            'partnerId'  => 11000,
        ],
        29360129 => [
            'wsdl'       => 'http://www.bettling.com/hive/accounting.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 12,
            'partnerId'  => 1,
        ],
        29360130 => [
            'wsdl'       => 'http://www.bettling.com/hive/accounting.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 12,
            'partnerId'  => 2,
        ],
        33554433 => [
            'wsdl'       => 'http://wallet.api.sekabet.com/Hive/HiveClient.asmx?wsdl',
            'apiTimeout' => 5,
            'providerId' => 24,
            'partnerId'  => 1
        ],
        37748737 => [
            'wsdl'       => 'http://one.blueoceangaming.com:8238/api/seamless/hi?wsdl',
            'apiTimeout' => 5,
            'providerId' => 25,
            'partnerId'  => 1,
        ],
        37748738 => [
            'wsdl'       => 'http://dev.pantaloo.com/api/seamless/hi?wsdl',
            'apiTimeout' => 5,
            'providerId' => 25,
            'partnerId'  => 2,
        ],
        37748739 => [
            'wsdl'       => 'http://prelive.pantaloo.com/api/seamless/hi?wsdl',
            'apiTimeout' => 5,
            'providerId' => 25,
            'partnerId'  => 3,
        ],
        41943041 => [
            'post_url'   => 'https://jerome1.devel.goldenpalace.be/hive/remote',
            'apiTimeout' => 5,
            'providerId' => 29,
            'partnerId'  => 1,
        ],
        46137345 => [
            'wsdl'       => 'wsdls/doxxbet_own.wsdl',
            'apiTimeout' => 5,
            'providerId' => 30,
            'partnerId'  => 1,
        ],
        50331649 => [
            'wsdl'       => 'http://185.77.83.146/~frontoffice/dev_front/hivepoker/hiveSoapServer?wsdl',
            'apiTimeout' => 5,
            'providerId' => 32,
            'partnerId'  => 1,
        ]
    ];

    /**
     * @param int $skinId
     * @return array
     */
    public static function getTpiConfigs(int $skinId): array
    {
        return ConfigManager::checkIfArrayExists((string)$skinId, self::$tpiConfigs);
    }

}