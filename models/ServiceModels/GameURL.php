<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 20.04.2017
 * Time: 11:38
 */
declare(strict_types = 1);

namespace Models\ServiceModels;

/**
 * Class GameURL
 * @package Models\ServiceModels
 */
class GameURL implements IServiceModels
{
    /**
     * @var int $resultCode
     */
    public $resultCode;

    /**
     * @var string $url
     */
    public $url;
}
