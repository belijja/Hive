<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 19.05.2017
 * Time: 11:55
 */
declare(strict_types = 1);

namespace Providers;

class NetentProvider
{
    public function login(array $user)
    {
        $sessionId = $this->loginUser($user);

        return $sessionId;
    }

    private function loginUser(array $user)
    {
        $r = 32;
        return $r;
    }

}