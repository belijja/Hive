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
            case ConfigManager::getDb('database', true):
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDb('host', true) . ";dbname=" . ConfigManager::getDb('database', true) . ";charset=utf8", ConfigManager::getDb('user', true), ConfigManager::getDb('pass', true));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
            case ConfigManager::getDb('database', false):
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDb('host', false) . ";dbname=" . ConfigManager::getDb('database', false) . ";charset=utf8", ConfigManager::getDb('user', false), ConfigManager::getDb('pass', false));
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