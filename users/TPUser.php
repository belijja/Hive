<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 24.05.2017
 * Time: 14:30
 */
declare(strict_types = 1);

namespace Users;

class TPUser extends AbstractUsers
{
    /**
     * TPUser constructor.
     * @param array $user
     * @param array $config
     */
    public function __construct(array $user, array $config)
    {
        parent::__construct($user, $config);
    }

    public function userNewTransactionOk()
    {
        return !($this->user['rights'] & 0x08000000);
    }

}