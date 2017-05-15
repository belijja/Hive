<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 11.05.2017
 * Time: 16:02
 */
declare(strict_types = 1);

namespace Helpers\SoapHelpers;

interface ISoapClient
{
    /**
     * @param int $userId
     * @param int $skinId
     * @param \SoapClient|null $soapClient
     * @return \stdClass
     */
    public function getUserInfo(int $userId, int $skinId, \SoapClient $soapClient = null): /*array*/
    \stdClass;

}