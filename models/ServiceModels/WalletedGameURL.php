<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 20.04.2017
 * Time: 16:39
 */
declare(strict_types = 1);

namespace Models\ServiceModels;

/**
 * Class WalletedGameURL
 * @package Models\ServiceModels
 */
class WalletedGameURL implements IServiceModels
{
    /**
     * @var int $resultCode
     */
    public $resultCode;

    /**
     * @var string $url
     */
    public $url;

    /**
     * @var string $sessionId
     */
    public $sessionId;

    /**
     * @var string $walletSessionId
     */
    public $walletSessionId;

    /**
     * @var string $walletTicketId
     */
    public $walletTicketId;
}

