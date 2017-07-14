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
     * @param LogManager $logger
     * @param \SoapClient $soapClient
     * @return \stdClass
     */
    public function getUserInfo(int $userId, int $skinId, LogManager $logger, \SoapClient $soapClient): /*array*/
    \stdClass;

}