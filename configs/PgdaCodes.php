<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 07.06.2017
 * Time: 15:47
 */
declare(strict_types = 1);

namespace Configs;

use Helpers\ConfigHelpers\ConfigManager;

class PgdaCodes
{
    private static $pgdaPrefixCasinoCodesPrimary = [
        'create'         => 40002,
        //if you want to switch to default configs comment this primary value and put null on that place
        'createTest'     => 40000000,
        'transaction'    => 42001,
        'delete'         => 50001,
        'deleteTest'     => 50000000,
        'history'        => 105820,
        'sessionBalance' => 59000
    ];

    private static $pgdaPrefixCasinoCodesDefault = [
        'create'         => 40000,
        'createTest'     => 40000000,
        'transaction'    => 42000,
        'delete'         => 50000,
        'deleteTest'     => 50000000,
        'history'        => 1058000,
        'sessionBalance' => 59000
    ];

    private static $pgdaServerPathSuffixPrimary = [//primary and default are the same?
        'cash'       => 'SH',
        'tournament' => 'TR',
        'casino'     => 'QF',
        '580'        => '580',
        '780'        => '780'
    ];

    private static $pgdaServerPathSuffixDefault = [
        'cash'       => 'SH',
        'tournament' => 'TR',
        'casino'     => 'QF',
        '580'        => '580',
        '780'        => '780'
    ];

    private static $pgdaAamsPrimary = [
        'conc'         => 15242,
        'fsc'          => 70,
        'maxSendTries' => 3
    ];
    private static $pgdaAamsDefault = [
        'conc'         => 15079,
        'fsc'          => 89,
        'maxSendTries' => 3
    ];

    public static function getPgdaAamsCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsDefault) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsDefault);
    }

    public static function getPgdaServerPathCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerPathSuffixPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerPathSuffixPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerPathSuffixDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaCasinoCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesDefault);
    }
}