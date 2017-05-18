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
    private static $dbTypes = [];
    private $pdo;

    /**
     * Db constructor.
     * @param $db
     */
    private function __construct($db)
    {
        switch ($db) {
            case ConfigManager::getDbDatabase(true):
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDbHost(true) . ";dbname=" . ConfigManager::getDbDatabase(true) . ";charset=utf8", ConfigManager::getDbUser(true), ConfigManager::getDbPass(true));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
            case ConfigManager::getDbDatabase(false):
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDbHost(false) . ";dbname=" . ConfigManager::getDbDatabase(false) . ";charset=utf8", ConfigManager::getDbUser(false), ConfigManager::getDbPass(false));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
        }
        self::$dbTypes[$db] = $this->pdo;
    }

    /**
     * @throws \Exception
     */
    private function __clone()
    {
        throw new \SoapFault('CODE_ERROR', 'You can not clone ' . __CLASS__ . ' class.');
    }

    /**
     * @param $db
     * @return \PDO
     */
    public static function getInstance($db): \PDO
    {
        if (!array_key_exists($db, self::$dbTypes)) {
            self::$dbTypes[$db] = new self($db);
        }
        return self::$dbTypes[$db]->pdo;

    }
}