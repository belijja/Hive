<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 07.06.2017
 * Time: 14:25
 */
declare(strict_types = 1);

namespace Pgda;

use Configs\PgdaCodes;
use Helpers\ConfigHelpers\ConfigManager;
use Models\PgdaModels;
use Pgda\Messages\Message400;

class PGDAIntegration
{
    private $pgdaModels;

    /**
     * PGDAIntegration constructor.
     * @param PgdaModels $pgdaModels
     */
    public function __construct(PgdaModels $pgdaModels)
    {
        $this->pgdaModels = $pgdaModels;
        $this->pgdaModels->prefixCasinoCreateTest = PgdaCodes::getPgdaCasinoCodes('createTest');
        $this->pgdaModels->prefixCasinoCreate = PgdaCodes::getPgdaCasinoCodes('create');
        $this->pgdaModels->prefixCasinoTransaction = PgdaCodes::getPgdaCasinoCodes('transaction');
        $this->pgdaModels->prefixCasinoDelete = PgdaCodes::getPgdaCasinoCodes('delete');
        $this->pgdaModels->prefixCasinoDeleteTest = PgdaCodes::getPgdaCasinoCodes('deleteTest');
        $this->pgdaModels->prefixCasinoHistory = PgdaCodes::getPgdaCasinoCodes('history');
        $this->pgdaModels->prefixCasinoSessionBalance = PgdaCodes::getPgdaCasinoCodes('sessionBalance');

        $this->pgdaModels->serverPathSuffixCash = PgdaCodes::getPgdaServerCodes('cashPath');
        $this->pgdaModels->serverPathSuffixTournament = PgdaCodes::getPgdaServerCodes('tournamentPath');
        $this->pgdaModels->serverPathSuffixCasino = PgdaCodes::getPgdaServerCodes('casinoPath');
        $this->pgdaModels->serverPathSuffix580 = PgdaCodes::getPgdaServerCodes('580Path');
        $this->pgdaModels->serverPathSuffix780 = PgdaCodes::getPgdaServerCodes('780Path');
    }




    /**
     * @param int $aamsGameCode
     * @param int $aamsGameType
     * @param string $sessionId
     * @param string $datetime
     * @param bool|null $isFun
     * @param bool|null $gameTesting
     * @return array
     * @throws \SoapFault
     */
    //start of session message 400
    public function casinoCreate(int $aamsGameCode, int $aamsGameType, string $sessionId, string $datetime, bool $isFun = null, bool $gameTesting = null): array
    {
        $parsedDate = $this->getPgdaDateAsArray($datetime);
        $endDate = $this->getPgdaEndDateAsArray($datetime);
        if ($gameTesting) {
            $transactionCode = $this->getPgdaTransactionId($this->pgdaModels->prefixCasinoCreateTest, $sessionId);
        } else {
            $transactionCode = $this->getPgdaTransactionId($this->pgdaModels->prefixCasinoCreate, $sessionId);
        }
        $message = Message400::getInstance(400);
        $message->setSessionId($transactionCode);
        $message->setStartYear($parsedDate['year']);
        $message->setStartMonth($parsedDate['month']);
        $message->setStartDay($parsedDate['day']);
        $message->setStartHour($parsedDate['hour']);
        $message->setStartMinute($parsedDate['minute']);
        $message->setStartSecond($parsedDate['second']);
        $message->setEndYear($endDate['year']);
        $message->setEndMonth($endDate['mon']);
        $message->setEndDay($endDate['mday']);
        try {
            if ($isFun) {
                $message->setAttribute('BON', 'F');
            } else {
                $message->setAttribute('BON', 'B');
            }
            $returnCode = $message->send($transactionCode, $aamsGameCode, $aamsGameType, $this->pgdaModels->serverPathSuffixCasino);
        } catch (\Exception $exception) {
            throw new \SoapFault('PGDA_ERROR', $exception->getMessage());
        }
        return [];
    }

    /**
     * @param string $prefix
     * @param string $id
     * @return string
     */
    private function getPgdaTransactionId(string $prefix, string $id): string
    {
        $p = pack("V", $prefix);
        for ($i = 0; $i < 8; $i++) {
            $p .= chr(bcmod($id, '256'));
            $id = bcdiv($id, '256');
        }
        $b64 = base64_encode($p);
        $b64 = str_replace("=", "-", $b64);
        $b64 = str_replace("+", ".", $b64);
        $b64 = str_replace("/", "_", $b64);
        return $b64;
    }

    private function getPgdaEndDateAsArray(string $datetime): array
    {
        $duration = ConfigManager::getPgda('sessionDuration') != '' ? ConfigManager::getPgda('sessionDuration') : '+5 days';
        return getdate(strtotime($duration, strtotime($datetime)));

    }

    /**
     * @param string $date
     * @return array
     */
    private function getPgdaDateAsArray(string $date): array
    {
        $parsedDate = date_parse($date);
        if (!$parsedDate) {
            error_log('PGDA: Invalid date! ' . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__ . ' VARIABLE: ' . var_export($parsedDate, true));
            exit;
        }
        return $parsedDate;
    }

}