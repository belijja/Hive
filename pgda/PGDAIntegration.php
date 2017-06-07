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

class PGDAIntegration
{
    /**
     * @param int $aamsGameCode
     * @param int $aamsGameType
     * @param int $sessionId
     * @param string $date
     * @param bool|null $isFun
     * @param bool|null $gameTesting
     * @return array
     */
    //message 400
    public function casinoCreate(int $aamsGameCode, int $aamsGameType, int $sessionId, string $datetime, bool $isFun = null, bool $gameTesting = null): array
    {
        $parsedDate = $this->getPgdaDateAsArray($datetime);
        $endDate = $this->getPgdaEndDateAsArray($datetime);
        $r = PgdaCodes::getPgdaCasinoCodes('prefixCasinoCreate');
        if ($gameTesting) {
            $transactionCode = $this->getPgdaTransactionId();
        } else {
            $transactionCode = $this->getPgdaTransactionId();
        }
        return [];
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