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
        'createTest'     => 40000000,
        'transaction'    => 42001,
        'delete'         => 50001,
        'deleteTest'     => 50000000,
        'history'        => 105820,
        'sessionBalance' => 59000
    ];

    private static $pgdaPrefixCasinoCodesSecondary = [
        'create'         => 40000,
        'createTest'     => 40000000,
        'transaction'    => 42000,
        'delete'         => 50000,
        'deleteTest'     => 50000000,
        'history'        => 1058000,
        'sessionBalance' => 59000
    ];

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaCasinoCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixCasinoCodesSecondary);
    }
}