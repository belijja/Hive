<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 24.05.2017
 * Time: 14:30
 */
declare(strict_types = 1);

namespace Users;

use BackOffice\Bonus;

class TPUser extends AbstractUsers
{
    /**
     * TPUser constructor.
     * @param array $user
     * @param array $config
     * @param Bonus $bonus
     */
    public function __construct(array $user, array $config, Bonus $bonus)
    {
        parent::__construct($user, $config, $bonus);
    }

}