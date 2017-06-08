<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 08.06.2017
 * Time: 14:29
 */
declare(strict_types = 1);

namespace Pgda\Messages;

class Message400 extends AbstractMessage
{
    private $attributeMultiplies = [];
    private $arrayStruct = [
        'sessionId',
        'endDay',
        'endMonth',
        'endYear',
        'attributeNumber'
    ];
    private $sessionId;
    private $startDay;
    private $startMonth;
    private $startYear;
    private $startHour;
    private $startMin;
    private $startSec;
    private $endDay;
    private $endMonth;
    private $endYear;
    private $attributeNumber;

    /**
     * @return Message400
     */
    public static function getInstance()
    {
        return new Message400();
    }

    /**
     * @param string $transactionCode
     * @return void
     */
    public function setSessionId(string $transactionCode): void
    {
        $this->sessionId = !empty($transactionCode) ? $transactionCode : null;
    }

    /**
     * @param int $startDay
     * @return void
     */
    public function setStartDay(int $startDay): void
    {
        $this->startDay = !empty($startDay) ? $startDay : null;
    }

    /**
     * @param int $startMonth
     * @return void
     */
    public function setStartMonth(int $startMonth): void
    {
        $this->startMonth = !empty($startMonth) ? $startMonth : null;
    }

    /**
     * @param int $startYear
     * @return void
     */
    public function setStartYear(int $startYear): void
    {
        $this->startYear = !empty($startYear) ? $startYear : null;
    }

    /**
     * @param int $startHour
     * @return void
     */
    public function setStartHour(int $startHour): void
    {
        $this->startHour = !empty($startHour) ? $startHour : null;
    }

    /**
     * @param int $startMin
     * @return void
     */
    public function setStartMinute(int $startMin): void
    {
        $this->startMin = !empty($startMin) ? $startMin : null;
    }

    /**
     * @param int $startSec
     * @return void
     */
    public function setStartSecond(int $startSec): void
    {
        $this->startSec = !empty($startSec) ? $startSec : null;
    }

    /**
     * @param int $endDay
     * @return void
     */
    public function setEndDay(int $endDay): void
    {
        $this->endDay = $endDay;
    }

    /**
     * @param int $endMonth
     * @return void
     */
    public function setEndMonth(int $endMonth): void
    {
        $this->endMonth = $endMonth;
    }

    /**
     * @param int $endYear
     * @return void
     */
    public function setEndYear(int $endYear): void
    {
        $this->endYear = $endYear;
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute(string $name, string $value): void
    {
        if (count($this->attributeMultiplies) > $this->attributeNumber) {
            throw new \OverflowException("Attribute number defined " . $this->attributeNumber . " overflow multiple attribute couple number. Error in: " . __CLASS__ . " in method " . __METHOD__ . " in line: " . __LINE__);
        }
        $this->attributeMultiplies[$name] = $value;
        $this->attributeNumber++;
    }

}