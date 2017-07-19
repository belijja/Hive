<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 21.04.2017
 * Time: 10:25
 */
declare(strict_types = 1);

namespace Services;

use Containers\ServiceContainer;

/**
 * Class Db
 * @package Helpers\ConfigHelpers
 */
class Db
{
    use ServiceContainer;
    public $pdo;
    public $x = 0;

    /**
     * @param bool $isFirstDbFromConfigFile
     * @return \PDO
     */
    public function getDb(bool $isFirstDbFromConfigFile): \PDO
    {
        switch ($isFirstDbFromConfigFile) {
            case true:
                try {
                    $this->pdo = new \PDO("mysql:host=" . $this->container->get('Config')->getDb('host', true) . ";dbname=" . $this->container->get('Config')->getDb('database', true) . ";charset=utf8", $this->container->get('Config')->getDb('user', true), $this->container->get('Config')->getDb('pass', true));
                } catch (\PDOException $ex) {
                    echo $ex->getMessage();
                }
            break;
            case false:
                try {
                    $this->pdo = new \PDO("mysql:host=" . $this->container->get('Config')->getDb('host', false) . ";dbname=" . $this->container->get('Config')->getDb('database', false) . ";charset=utf8", $this->container->get('Config')->getDb('user', false), $this->container->get('Config')->getDb('pass', false));
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