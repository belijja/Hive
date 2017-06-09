<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 08.06.2017
 * Time: 16:26
 */
declare(strict_types = 1);

namespace Pgda\Messages;

use Pgda\Fields\PField;
use Pgda\Fields\UField;

class AbstractMessage
{
    protected $aamsGioco;
    protected $aamsGiocoId;

    protected $stack = [];

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
     * @return void
     */
    private function buildMessage(): void
    {
        $this->prepare();
        /*$this->writeBody();
        $this->writeHeader();*/
    }

    /**
     * @param string|null $transactionCode
     * @return void
     */
    public function setTransactionCode(string $transactionCode = null): void
    {
        $this->transactionCode = $transactionCode;
    }

    /**
     * @param int $aamsGameCode
     * @return void
     */
    public function setAamsGameCode(int $aamsGameCode): void
    {
        $this->aamsGioco = intval($aamsGameCode);
    }

    /**
     * @param int $aamsGameType
     * @return void
     */
    public function setAamsGameType(int $aamsGameType): void
    {
        $this->aamsGiocoId = $aamsGameType;
    }

    protected function attach($field)
    {
        if (!$field instanceof PField && !$field instanceof UField) {
            throw new \BadMethodCallException('Error, ' . __METHOD__ . " can only accept instances of PField and UField on line: " . __LINE__);
        }
        $this->stack[] = $field;
    }

}