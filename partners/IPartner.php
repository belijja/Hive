<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 10.05.2017
 * Time: 12:14
 */
declare(strict_types = 1);

namespace Partners;

interface IPartner
{
    /**
     * @param int $userId
     * @param int $skinId
     * @param int $partnerId
     * @param \SoapClient|null $soapClient
     * @return void
     */
    public function checkAndRegisterUser(int $userId, int $skinId, int $partnerId, \SoapClient $soapClient = null): void;
}