<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 08.06.2017
 * Time: 16:26
 */
declare(strict_types = 1);

namespace Pgda\Messages;

class AbstractMessage
{
    protected $transactionCode;
    protected $binaryMessage;
    protected $headerMessageEncoded;
    protected $bodyMessageEncoded;
    protected $headerMessageDecoded;
    protected $bodyMessageDecoded;

    public function send(string $transactionCode, int $aamsGameCode, int $aamsGameType, string $serverPathSuffix)
    {
        $this->setTransactionCode($transactionCode);
        $this->setAamsGameCode($aamsGameCode);
        $this->setAamsGameType($aamsGameType);
        $this->buildMessage();
    }

    /**
     * @param string|null $transactionCode
     * @return void
     */
    public function setTransactionCode(string $transactionCode = null): void
    {
        $this->transactionCode = $transactionCode;
    }

}