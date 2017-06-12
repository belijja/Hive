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
    private $aamsGioco;
    private $aamsGiocoId;
    protected $messageId;

    private $stack = [];
    private $positionEnds = [];

    private $transactionCode;
    private $binaryMessage;
    private $headerMessageEncoded;
    private $bodyMessageEncoded;
    private $headerMessageDecoded;
    private $bodyMessageDecoded;

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
        $this->writeBody($this);
        //$this->writeHeader();
    }

    private function writeBody($message)
    {//continue
        $errorMessage = ["\nPacking: "];
        $types = "";
        $values = [];
        $array64Bits = [];
        foreach ($message as $fieldPosition => $field) {
            $errorMessage[] = $field->name . " = " . $field->value;
            if ($field->invoke === PField::bigint) {
                //create real 8 Byte String of Big Int
                $stringBinaryBigInt = $this->write64BitIntegers($field->value);
                //set presence of Big Int in Position $fieldPosition with their binary Calculated Value
                $array64Bits[$fieldPosition] = $stringBinaryBigInt;
                //we have also to create 2 fake 4 Bytes int
                //to pass to PHP Pack() function to reserve a 8 Byte space
                //fake hWord
                $fakeHighWord = 0x00;
                //fake loWord
                $fakeLowWord = 0x00;
                $values[] = $fakeHighWord;
                $values[] = $fakeLowWord;
            } else {
                $values [] = $field->value;
            }
            $types .= $field->invoke;
        }
        $binaryString = call_user_func_array("pack", array_merge([$types], $values));

        //now replace the Fake Big Int With the real calculated
        foreach ($array64Bits as $fieldPos => $binaryValue) {
            $binaryString = substr_replace($binaryString, $binaryValue, $message->getPositionField($fieldPos), 8);
        }

        $this->errorMessage['_write'] [] = $errorMessage;

        return $binaryString;
    }

    private function write64BitIntegers($bigIntValue)
    {
        if (PHP_INT_SIZE > 4) {
            settype($bigIntValue, 'integer');
            $binaryString = chr($bigIntValue >> 56 & 0xFF) . chr($bigIntValue >> 48 & 0xFF) . chr($bigIntValue >> 40 & 0xFF) . chr($bigIntValue >> 32 & 0xFF) . chr($bigIntValue >> 24 & 0xFF) . chr($bigIntValue >> 16 & 0xFF) . chr($bigIntValue >> 8 & 0xFF) . chr($bigIntValue & 0xFF);

        } else {
            throw new \LengthException('Write error. This Processor can not handle 64bit integers without loss of significant digits. Error in: ' . __METHOD__ . " on line " . __LINE__);
        }

        return $binaryString;

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
        $actualPosition = count($this->stack) - 1;
        if (!empty ($this->positionEnds)) {
            $this->positionEnds [$actualPosition] = $this->positionEnds [$actualPosition - 1] + $field->typeLength;
        } else {
            $this->positionEnds [$actualPosition] = $field->typeLength;
        }
    }

}