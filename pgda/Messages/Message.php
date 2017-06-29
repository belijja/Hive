<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 08.06.2017
 * Time: 16:26
 */
declare(strict_types = 1);

namespace Pgda\Messages;

use Configs\PgdaCodes;
use Pgda\Fields\AbstractField;
use Pgda\Fields\PField;
use Pgda\Fields\UField;
use Pgda\PGDAIntegration;

class Message implements \Iterator
{
    private $position = 0;
    private $errorMessage = [
        'write' => [],
        'read'  => []
    ];
    protected $messageId;
    private $aamsGioco;
    private $aamsGiocoId;
    private $stack = [];
    private $positionEnds = [];
    private $transactionCode;
    private $binaryMessage;
    private $headerMessageEncoded;
    private $bodyMessageEncoded;
    private $headerMessageDecoded;
    private $bodyMessageDecoded;

    //CommunicationAdapter class properties
    private static $__Instance = null;
    public $url;
    private $HTTPsocket;
    private $outgoingMessage;
    private $GAME_TYPE = 2; //1 for Tournaments, 3 for Cash Game ( AAMS protoc. 2 )
    private $FLUX_METHOD = 2; //modalita' -> //1 condizionata //2 non condizionata //4 Cash Game
    private $AAMS_GIOCO;
    private $suffix;
    const CODICE_RETE = 2;
    private static $RecursionCounter = 0;
    //connectionClass class properties
    private $err = null;
    private $store;
    private $header = null;

    public function current()
    {
        return $this->stack [$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset ($this->stack [$this->position]);
    }

    /**
     * @return string
     */
    private function getHeader(): string
    {
        return $this->headerMessageEncoded;
    }

    /**
     * @param string $binaryResponse
     * @return void
     */
    private function setBinaryResponse(string $binaryResponse): void
    {
        $this->binaryMessage = $binaryResponse;
    }

    /**
     * @return string
     */
    private function getBody(): string
    {
        return $this->bodyMessageEncoded;
    }

    private function getMessage(): string
    {
        return $this->store;
    }

    public function send(string $transactionCode, int $aamsGameCode, int $aamsGameType, string $serverPathSuffix)
    {
        $this->setTransactionCode($transactionCode);
        $this->setAamsGameCode($aamsGameCode);
        $this->setAamsGameType($aamsGameType);
        $this->buildMessage();
        $binaryMessage = $this->getHeader() . $this->getBody();
        $this->sendMessageRecursive($binaryMessage, $this->getBody(), $serverPathSuffix);
    }

    private function sendMessageRecursive(string $binaryMessage, string $oldEncodedBody, string $serverPathSuffix, int $cnt = 0)
    {
        $cnt++;
        try {
            $this->sendMessage($binaryMessage, $serverPathSuffix);
            $recordMessage = $this->getMessage();
        } catch (\Exception $exception) {
            switch ($exception->getCode()) {
                case -42:// header not found
                    error_log("PGDA header not found: " . $exception->getMessage());
                    if ($cnt <= 3) {
                        $this->setTransactionCode(PGDAIntegration::getPgdaTransactionId(PgdaCodes::getPgdaPrefix('retry'), (string)(microtime(true) * 10000)));
                        $this->writeHeader();
                        $binaryMessage = $this->getHeader() . $this->getBody();
                        sleep(1);
                        $this->sendMessageRecursive($binaryMessage, $this->getBody(), $serverPathSuffix, $cnt);
                    }
                break;
                default:// HTTP error
                    error_log("PGDA exception, HTTP CODE: " . $exception->getCode());
                    if ($cnt <= 3) {
                        sleep(1);
                        $this->sendMessageRecursive($binaryMessage, $this->getBody(), $serverPathSuffix, $cnt);
                    }
                break;
            }
            return -42;
        }
        $this->setBinaryResponse($recordMessage);
        $this->decodeResponse();
    }

    /**
     * @param string $binaryMessage
     * @param string $serverPathSuffix
     * @return bool
     * @throws \Exception
     */
    private function sendMessage(string $binaryMessage, string $serverPathSuffix): bool
    {
        $signedData = $this->signData($binaryMessage);
        $this->url = PgdaCodes::getPgdaServerCodes('scheme') . "://" . PgdaCodes::getPgdaServerCodes('address') . ":" . PgdaCodes::getPgdaServerCodes('port') . PgdaCodes::getPgdaServerCodes('path') . $serverPathSuffix;
        $curl = curl_init($this->url);
        $options = [
            CURLOPT_POSTFIELDS     => $signedData,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $transferInfo = curl_getinfo($curl);
        curl_close($curl);
        if ($transferInfo['http_code'] != 200) {
            throw new \Exception("HTTP status code " . $transferInfo['http_code'], $transferInfo['http_code']);
        }
        $this->store = $this->extract($response, $binaryMessage);
        return true;
    }

    /**
     * @param string $curlResponse
     * @param string $binaryMessage
     * @return string
     * @throws \Exception
     */
    private function extract(string $curlResponse, string $binaryMessage): string
    {
        $shortBinaryMessage = substr($binaryMessage, 0, 38);
        $i = strpos($curlResponse, $shortBinaryMessage);
        if ($i === false) {
            throw new \Exception("Header not found in reply. MESSAGE: $curlResponse", -42);
        }
        $binaryLength = ord($curlResponse{$i + 38}) * 256 * 256 * 256 + ord($curlResponse{$i + 39}) * 256 * 256 + ord($curlResponse{$i + 40}) * 256 + ord($curlResponse{$i + 41});
        return substr($curlResponse, $i, 42 + $binaryLength);
    }

    private function signData($binaryMessage)
    {
        $binaryIn = tempnam(sys_get_temp_dir(), uniqid() . '_AAMSmsg_' . md5(microtime()));
        $signedOut = tempnam(sys_get_temp_dir(), uniqid() . '_Signed_AAMSmsg_' . md5(microtime()));
        file_put_contents($binaryIn, $binaryMessage);
        $stringKey = openssl_pkey_get_private('file://' . PgdaCodes::getPgdaCertificates('private'), PgdaCodes::getPgdaCertificates('privatePassword'));
        openssl_pkcs7_sign($binaryIn, $signedOut, file_get_contents(PgdaCodes::getPgdaCertificates('private')), $stringKey, [], PKCS7_BINARY | PKCS7_NOINTERN | PKCS7_NOCERTS);
        $returnString = file_get_contents($signedOut);
        //Remove all mime headers from signed message
        $arrayMsg = explode("\n", $returnString);
        foreach ($arrayMsg as $key => $strings) {
            if (($strings == '') || (stripos($strings, 'content') !== false) || (stripos($strings, 'mime') !== false)) {
                unset($arrayMsg[$key]);
            }
        }
        $returnString = base64_decode(implode("\n", $arrayMsg));
        unlink($binaryIn);
        unlink($signedOut);
        return $returnString;
    }

    /**
     * @return void
     */
    private function buildMessage(): void
    {
        $this->prepare();
        $this->bodyMessageEncoded = $this->writeBody($this);
        $this->headerMessageEncoded = $this->writeHeader();
    }

    private function writeHeader()
    {
        if (is_null($this->bodyMessageEncoded)) {
            throw new \LogicException("Can not write header packet while bodyMessage is not written. Error in " . __METHOD__ . " on line: " . __LINE__);
        }
        if (empty($this->messageId)) {
            throw new \LogicException ("Can not write header packet while message Id is not defined. Error in " . __METHOD__ . " on line: " . __LINE__);
        }
        if ($this->messageId >= 800) {
            $this->aamsGiocoId = 0;
            $this->aamsGioco = 0;
        } else {
            if (empty ($this->aamsGiocoId)) {
                throw new \LogicException ("Can not write header packet while aamsGiocoId is not defined. Error in " . __METHOD__ . " on line: " . __LINE__);
            }
            if (is_null($this->aamsGioco)) {
                throw new \LogicException ("Can not write header packet while aamsGioco is not defined Error in " . __METHOD__ . " on line: " . __LINE__);
            }
        }
        error_log("AAMS_CONC: " . PgdaCodes::getPgdaAamsCodes('conc') . " AAMS_FSC: " . PgdaCodes::getPgdaAamsCodes('fsc') . " AAMS_GIOCO: " . $this->aamsGioco . " TRANSACTION_ID: " . $this->transactionCode . " MSG_TYPE: " . $this->messageId);
        $messageHeader = new Message();
        $messageHeader->attach(PField::set("Num. vers. Protoc.", PField::byte, 2));
        $messageHeader->attach(PField::set("Cod. Forn. Servizi", PField::int, PgdaCodes::getPgdaAamsCodes('fsc')));
        $messageHeader->attach(PField::set("Cod. Conc. Trasm.", PField::int, PgdaCodes::getPgdaAamsCodes('conc')));
        $messageHeader->attach(PField::set("Cod. Conc. Propo.", PField::int, PgdaCodes::getPgdaAamsCodes('conc')));
        $messageHeader->attach(PField::set("Codice Gioco.", PField::int, $this->aamsGioco));
        $messageHeader->attach(PField::set("Cod. Tipo Gioco.", PField::byte, $this->aamsGiocoId));
        $messageHeader->attach(PField::set("Tipo Mess.", PField::string, $this->messageId, 4));
        $messageHeader->attach(PField::set("Codice transazione", PField::string, $this->getTransactionCode(), 16));
        $messageHeader->attach(PField::set("Lunghezza Body", PField::int, strlen($this->bodyMessageEncoded)));
        return $this->writeBody($messageHeader);
    }

    public function getTransactionCode()
    {
        if (empty($this->transactionCode)) {
            throw new \UnexpectedValueException('Tried to get an EMPTY Transaction Code. Use ::setTransactionCode() First. Error on: ' . __METHOD__ . " in " . __FILE__);
        }
        return $this->transactionCode;
    }

    /**
     * @param Message $message
     * @return string
     */
    private function writeBody(Message $message): string
    {
        $errorMessage = ["\nPacking: "];
        $types = "";
        $values = [];
        $array64Bits = [];
        foreach ($message as $fieldPosition => $field) {
            $errorMessage[] = $field->name . " = " . $field->value;
            if ($field->invoke === PField::bigint) {
                //create real 8 byte string of big int
                $stringBinaryBigInt = $this->write64BitIntegers((string)$field->value);
                //set presence of Big Int in Position $fieldPosition with their binary Calculated Value
                $array64Bits[$fieldPosition] = $stringBinaryBigInt;
                //create 2 fake 4 bytes int
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

        //now replace the fake big int with the real calculated
        foreach ($array64Bits as $fieldPos => $binaryValue) {
            $binaryString = substr_replace($binaryString, $binaryValue, $message->getPositionField($fieldPos), 8);
        }
        $this->errorMessage['write'][] = $errorMessage;
        return $binaryString;
    }

    /**
     * @param int $fieldNum
     * @return int
     */
    public function getPositionField(int $fieldNum): int
    {
        if (!array_key_exists($fieldNum, $this->positionEnds)) {
            throw new \OutOfBoundsException("Can't find a field in Position $fieldNum - Error in: " . __METHOD__ . " on line " . __LINE__);
        }
        return $this->positionEnds[$fieldNum] - ($this->stack[$fieldNum]->typeLength);
    }

    /**
     * @param string|null $bigIntValue
     * @return string
     */
    private function write64BitIntegers(string $bigIntValue = null): string
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

    protected function attach(AbstractField $field): void
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