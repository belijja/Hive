<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 10.05.2017
 * Time: 12:14
 */
declare(strict_types = 1);

namespace Partners;

use Helpers\ServerHelpers\ServerManager;
use Helpers\ConfigHelpers\Db;
use Helpers\SoapHelpers\ISoapClient;

abstract class AbstractPartners
{
    protected $serverManager;
    protected $db;
    protected $soapClient;

    /**
     * AbstractPartners constructor.
     * @param ServerManager $serverManager
     * @param ISoapClient $soapClient
     */
    public function __construct(ServerManager $serverManager, ISoapClient $soapClient)
    {
        $this->serverManager = $serverManager;
        $this->db = Db::getInstance()->pdo;
        $this->soapClient = $soapClient;
    }

    /**
     * @param array $arrayOfParams
     * @return array
     */
    public abstract function checkAndRegisterUser(array $arrayOfParams): array;
}