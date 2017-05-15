<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 20.04.2017
 * Time: 14:48
 */
declare(strict_types = 1);

namespace Helpers\ConfigHelpers;

/**
 * Class ConfigManager
 * @package Helpers\ConfigHelpers
 */
class ConfigManager
{
    private static $db;
    private static $thirdPartyServicePartners;
    private static $wsdl;
    private static $ISBets;
    private static $server;

    /**
     * @throws \SoapFault
     */
    private static function parseConfigFile()
    {
        if (!$config = parse_ini_file('config.ini', true)) {
            $error = error_get_last();
            throw new \SoapFault('CONFIG_ERROR', "Error occurred while parsing configuration file: " . $error['message'] . " in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        self::$db = $config['DATABASE'];
        self::$thirdPartyServicePartners = $config['THIRD PARTY SERVICE PARTNERS'];
        self::$wsdl = $config['WSDL'];
        self::$ISBets = $config['ISBETS'];
        self::$server = $config['SERVER'];
    }

    /**
     * @param string $configKey
     * @param array $configArray
     * @return string
     * @throws \SoapFault
     */
    public static function checkIfKeyExists(string $configKey, array $configArray): string
    {
        if (array_key_exists((string)$configKey, $configArray)) {
            return (string)$configArray[$configKey];
        } else {
            throw new \SoapFault('INVALID_ARG', "Argument " . $configKey . " doesn't exists in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
    }

    /**
     * @param string $configKey
     * @param array $configArray
     * @return array
     * @throws \SoapFault
     */
    public static function checkIfArrayExists(string $configKey, array $configArray): array
    {
        if (array_key_exists($configKey, $configArray) && is_array($configArray[$configKey])) {
            return $configArray[$configKey];
        } else {
            throw new \SoapFault('INVALID_ARG', "Argument " . $configKey . " doesn't exists in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
    }

    /**
     * @return string
     */
    public static function getServerAddress(): string
    {
        self::parseConfigFile();
        return self::$server['address'];
    }

    /**
     * @return string
     */
    public static function getServerPort(): string
    {
        self::parseConfigFile();
        return self::$server['port'];
    }

    /**
     * @return string
     */
    public static function getServerNePort(): string
    {
        self::parseConfigFile();
        return self::$server['nePort'];
    }

    /**
     * @return string
     */
    public static function getISBetsApiConnectionTimeout(): string
    {
        self::parseConfigFile();
        return self::$ISBets['apiConnectionTimeout'];
    }

    /**
     * @return string
     */
    public static function getISBetsApiUri(): string
    {
        self::parseConfigFile();
        return self::$ISBets['apiUri'];
    }

    /**
     * @return string
     */
    public static function getISBetsApiPass(): string
    {
        self::parseConfigFile();
        return self::$ISBets['apiPassword'];
    }

    /**
     * @return string
     */
    public static function getISBetsApiAccount(): string
    {
        self::parseConfigFile();
        return self::$ISBets['apiAccount'];
    }

    /**
     * @return string
     */
    public static function getISBetsProviderId(): string
    {
        self::parseConfigFile();
        return self::$ISBets['localProviderId'];
    }

    /**
     * @return string
     */
    public static function getWsdlCacheDir(): string
    {
        self::parseConfigFile();
        return self::$wsdl['wsdlCacheDir'];
    }

    /**
     * @param $key
     * @return array
     */
    public static function getThirdPartyServicePartners($key): array
    {
        self::parseConfigFile();
        return self::$thirdPartyServicePartners[$key];
    }

    /**
     * @return string
     */
    public static function getDbHost(): string
    {
        self::parseConfigFile();
        return self::$db['host'];
    }

    /**
     * @return string
     */
    public static function getDbDatabase(): string
    {
        self::parseConfigFile();
        return self::$db['database'];
    }

    /**
     * @return string
     */
    public static function getDbUser(): string
    {
        self::parseConfigFile();
        return self::$db['user'];
    }

    /**
     * @return string
     */
    public static function getDbPass(): string
    {
        self::parseConfigFile();
        return self::$db['pass'];
    }
}

