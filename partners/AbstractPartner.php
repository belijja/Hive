<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 10.05.2017
 * Time: 12:14
 */
declare(strict_types = 1);

namespace Partners;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ServerHelpers\ServerManager;
use Helpers\ConfigHelpers\Db;
use Helpers\SoapHelpers\ISoapClient;

abstract class AbstractPartner
{
    protected $serverManager;
    protected $db;
    protected $soapClient;
    protected $log;

    /**
     * AbstractPartners constructor.
     * @param ServerManager $serverManager
     * @param ISoapClient $soapClient
     */
    public function __construct(ServerManager $serverManager, ISoapClient $soapClient)
    {
        $this->serverManager = $serverManager;
        $this->db = Db::getInstance(ConfigManager::getDb('database', true));
        $this->soapClient = $soapClient;
    }

    /**
     * @param int $userId
     * @param int $skinId
     * @param int $partnerId
     * @param \SoapClient|null $soapClient
     * @return void
     */
    public abstract function checkAndRegisterUser(int $userId, int $skinId, int $partnerId, \SoapClient $soapClient = null): void;
}