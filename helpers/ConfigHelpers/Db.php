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
    public $pdo;
    public $x = 0;

    /**
     * @param bool $isFirstDbFromConfigFile
     * @return \PDO
     */
    public function getDb(bool $isFirstDbFromConfigFile)
    {
        switch ($isFirstDbFromConfigFile) {
            case true:
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDb('host', true) . ";dbname=" . ConfigManager::getDb('database', true) . ";charset=utf8", ConfigManager::getDb('user', true), ConfigManager::getDb('pass', true));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
            case false:
                try {
                    $this->pdo = new \PDO("mysql:host=" . ConfigManager::getDb('host', false) . ";dbname=" . ConfigManager::getDb('database', false) . ";charset=utf8", ConfigManager::getDb('user', false), ConfigManager::getDb('pass', false));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
        }
        error_log("Number " . $this->x);
        $this->x++;
        return $this->pdo;
    }
}