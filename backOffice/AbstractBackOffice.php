<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 18.05.2017
 * Time: 16:41
 */
declare(strict_types = 1);

namespace BackOffice;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ConfigHelpers\Db;

abstract class AbstractBackOffice
{
    protected $db;

    /**
     * AbstractBackOffice constructor.
     */
    public function __construct()
    {
        //$this->db = Db::getInstance(ConfigManager::getDb('database', false));
    }
}