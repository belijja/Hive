<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 17.05.2017
 * Time: 15:18
 */
declare(strict_types = 1);

namespace Helpers\SessionHelpers;

class SessionManager
{
    /**
     * @return string
     */
    public function startSessionAndGetSessionId(): string
    {
        @session_destroy();
        ini_set("session.use_cookies", '0');
        session_start();
        return session_id();
    }
}