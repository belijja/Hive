<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 16.05.2017
 * Time: 10:42
 */
declare(strict_types = 1);

namespace Users;

/**
 * Interface IUsers
 * @package Users
 */
interface IUsers
{
    /**
     * @param array $params
     * @return array
     */
    public function getUserData(array $params) : array ;
}