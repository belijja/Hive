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

    private static $pgdaServerPrimary = [//primary and default are the same?
        'scheme'         => 'http',
        'address'        => '81.2.205.160',
        'port'           => '80',
        'path'           => '/GiochiDiAbilitaV2_1/ServletFactoryFirma_V213_',
        'cashPath'       => 'SH',
        'tournamentPath' => 'TR',
        'casinoPath'     => 'QF',
        '580Path'        => '580',
        '780Path'        => '780'
    ];

    private static $pgdaServerDefault = [
        'scheme'         => 'http',
        'address'        => '81.2.205.160',
        'port'           => '80',
        'path'           => '/GiochiDiAbilitaV2_1/ServletFactoryFirma_V213_',
        'cashPath'       => 'SH',
        'tournamentPath' => 'TR',
        'casinoPath'     => 'QF',
        '580Path'        => '580',
        '780Path'        => '780'
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

    private static $pgdaCertificatesPrimary = [
        'private'         => 'pgda/Certificates/firma-2.pem',
        'privatePassword' => 'sks_test',
        'sogeiPublic'     => 'pgda/Certificates/sogei.cer'
    ];
    private static $pgdaCertificatesDefault = [
        'private'         => 'pgda/Certificates/firma_sks_test.pem',
        'privatePassword' => 'sks_test',
        'sogeiPublic'     => 'pgda/Certificates/sogei.cer'
    ];

    private static $pgdaPrefixPrimary = [
        'updateSessionEnd' => 81000,
        'sendModuleList'   => 83000,
        'retry'            => 42424242
    ];

    private static $pgdaPrefixDefault = [
        'updateSessionEnd' => 81000,
        'sendModuleList'   => 83000,
        'retry'            => 42424242
    ];

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaPrefix(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaPrefixDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaCertificates(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaCertificatesPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaCertificatesPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaCertificatesDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaAamsCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaAamsDefault);
    }

    /**
     * @param string $pgdaKey
     * @return string
     */
    public static function getPgdaServerCodes(string $pgdaKey): string
    {
        return (ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerPrimary) != '') ? ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerPrimary) : ConfigManager::checkIfKeyExists((string)$pgdaKey, self::$pgdaServerDefault);
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