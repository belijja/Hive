<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 20.04.2017
 * Time: 14:48
 */
declare(strict_types = 1);

namespace Services;

/**
 * Class ConfigManager
 * @package Helpers\ConfigHelpers
 */
class ConfigManager
{
    private $firstDb;
    private $secondDb;
    private $wsdl;
    private $SKS;
    private $server;
    private $netent;
    private $it;
    private $bonus;
    private $log;
    private $pgda;

    /**
     * ConfigManager constructor.
     * @throws \SoapFault
     */
    public function __construct()
    {
        if (!$config = parse_ini_file('config.ini', true)) {
            $error = error_get_last();
            throw new \SoapFault('CONFIG_ERROR', "Error occurred while parsing configuration file: " . $error['message'] . " in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        $this->firstDb = $config['DATABASE 1'];
        $this->secondDb = $config['DATABASE 2'];
        $this->wsdl = $config['WSDL'];
        $this->SKS = $config['SKS'];
        $this->server = $config['SERVER'];
        $this->netent = $config['NETENT'];
        $this->it = $config['IT'];
        $this->bonus = $config['BONUS'];
        $this->log = $config['LOG'];
        $this->pgda = $config['PGDA'];

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
    public function getPgda(string $key): string
    {
        return $this->pgda[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getLog(string $key): string
    {
        return $this->log[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getBonus(string $key): string
    {
        return $this->bonus[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getIT(string $key): string
    {
        return $this->it[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getNetent(string $key): string
    {
        return $this->netent[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getServer(string $key): string
    {
        return $this->server[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getSKS(string $key): string
    {
        return $this->SKS[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getWsdl(string $key): string
    {
        return $this->wsdl[$key];
    }

    /**
     * @param string $key
     * @param bool $firstDb
     * @return string
     */
    public function getDb(string $key, bool $firstDb): string
    {
        return $firstDb ? $this->firstDb[$key] : $this->secondDb[$key];
    }
}

