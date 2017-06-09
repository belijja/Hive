<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 08.06.2017
 * Time: 14:29
 */
declare(strict_types = 1);

namespace Pgda\Messages;

use Pgda\Fields\PField;

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

    /**
     * @return void
     */
    public function prepare(): void
    {
        foreach ($this->arrayStruct as $fieldVariableName) {
            if (is_null($this->$fieldVariableName)) {
                throw new \UnexpectedValueException($fieldVariableName . " value not defined. Error in: " . __METHOD__ . " on line " . __LINE__);
            }
        }
        $this->attach(PField::set("ID Sessione", PField::string, $this->sessionId, 16));
        $this->attach(PField::set("Start Day", PField::shortInt, (is_null($this->startDay) ? gmdate("d") : $this->startDay)));
        $this->attach(PField::set("Start Month", PField::shortInt, (is_null($this->startMonth) ? gmdate("m") : $this->startMonth)));
        $this->attach(PField::set("Start Year", PField::shortInt, (is_null($this->startYear) ? gmdate("Y") : $this->startYear)));
        $this->attach(PField::set("Start Hour", PField::shortInt, (is_null($this->startHour) ? gmdate("H") : $this->startHour)));
        $this->attach(PField::set("Start Minute", PField::shortInt, (is_null($this->startMin) ? gmdate("i") : $this->startMin)));
        $this->attach(PField::set("Start Second", PField::shortInt, (is_null($this->startSec) ? gmdate("s") : $this->startSec)));
        $this->attach(PField::set("End Day", PField::shortInt, $this->endDay));
        $this->attach(PField::set("End Month", PField::shortInt, $this->endMonth));
        $this->attach(PField::set("End Year", PField::shortInt, $this->endYear));
        $this->attach(PField::set("Attribute Num.", PField::int, $this->attributeNumber));
        if (count($this->attributeMultiplies) != $this->attributeNumber) {
            throw new \UnexpectedValueException("Attribute number defined does not match with multiple attribute couple number. Error in: " . __METHOD__ . " on line " . __LINE__);
        }
        foreach ($this->attributeMultiplies as $code => $value) {
            $this->attach(PField::set("$code", PField::string, $code, 3));
            $this->attach(PField::set("$code -> $value", PField::string, $value, 16));
        }
    }

}