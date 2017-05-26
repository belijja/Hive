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
    private static $firstDb;
    private static $secondDb;
    private static $thirdPartyServicePartners;
    private static $wsdl;
    private static $ISBets;
    private static $server;
    private static $netent;

    /**
     * @throws \SoapFault
     */
    public static function parseConfigFile()
    {
        if (!$config = parse_ini_file('config.ini', true)) {
            $error = error_get_last();
            throw new \SoapFault('CONFIG_ERROR', "Error occurred while parsing configuration file: " . $error['message'] . " in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        self::$firstDb = $config['DATABASE 1'];
        self::$secondDb = $config['DATABASE 2'];
        self::$wsdl = $config['WSDL'];
        self::$ISBets = $config['ISBETS'];
        self::$server = $config['SERVER'];
        self::$netent = $config['NETENT'];

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
     * @param string $key
     * @return string
     */
    public static function getNetent(string $key): string
    {
        return self::$netent[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getServer(string $key): string
    {
        return self::$server[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getISBets(string $key): string
    {
        return self::$ISBets[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getWsdl(string $key): string
    {
        return self::$wsdl[$key];
    }

    /**
     * @param string $key
     * @param bool $firstDb
     * @return string
     */
    public static function getDb(string $key, bool $firstDb): string
    {
        return $firstDb ? self::$firstDb[$key] : self::$secondDb[$key];
    }
}

