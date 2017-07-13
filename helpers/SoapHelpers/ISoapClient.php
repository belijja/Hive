<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 16:02
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

use Helpers\LogHelpers\LogManager;

interface ISoapClient
{
    /**
     * @param int $userId
     * @param int $skinId
     * @param \SoapClient $soapClient
     * @param LogManager $logger
     * @return \stdClass
     */
    public function getUserInfo(int $userId, int $skinId, \SoapClient $soapClient, LogManager $logger): /*array*/
    \stdClass;

}