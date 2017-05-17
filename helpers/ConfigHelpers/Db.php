<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 21.04.2017
 * Time: 10:25
 */
declare(strict_types = 1);

namespace Helpers\ConfigHelpers;

/**
 * Class Db
 * @package Helpers\ConfigHelpers
 */
class Db
{
    private static $instance = null;
    public $pdo;

    /**
     * Db constructor.
     */
    private function __construct()
    {
        try {
            $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDbHost(true) . ";dbname=" . ConfigManager::getDbDatabase(true) . ";charset=utf8", ConfigManager::getDbUser(true), ConfigManager::getDbPass(true));
        } catch (\PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function __clone()
    {
        throw new \SoapFault('CODE_ERROR', 'You can not clone ' . __CLASS__ . ' class.');
    }

    /**
     * @return Db
     */
    public static function getInstance(): Db
    {
        if (!self::$instance) {
            self::$instance = new Db();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }
}