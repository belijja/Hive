<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 09.05.2017
 * Time: 10:06
 */
declare(strict_types = 1);

namespace Configs;

use Helpers\ConfigHelpers\ConfigManager;

class CurrencyCodes
{
    private const  MONEY_SYMBOL = "&euro;";
    private const  CURRENCY_ID_ACCOUNT = 0;
    private const  CURRENCY_ID_EUR = 1;
    private const  CURRENCY_ID_USD = 2;
    private const  CURRENCY_ID_GBP = 3;
    private const  CURRENCY_ID_TRY = 4;
    private const  CURRENCY_ID_PLN = 5;
    private const  CURRENCY_ID_CHF = 6;
    private const  CURRENCY_ID_RON = 7;
    private const  CURRENCY_ID_BRL = 8;
    private const  CURRENCY_ID_ARS = 9;
    private const  CURRENCY_ID_UAH = 10;
    private const  CURRENCY_ID_PEN = 11;

    private static $currencyNames = [
        "1"  => "EUR",
        "2"  => "USD",
        "3"  => "GBP",
        "4"  => "TRY",
        "5"  => "PLN",
        "6"  => "CHF",
        "7"  => "RON",
        "8"  => "BRL",
        "9"  => "ARS",
        "10" => "UAH",
        "11" => "PEN"
    ];
    private static $currencyIds = [
        "EUR" => self::CURRENCY_ID_EUR,
        "USD" => self::CURRENCY_ID_USD,
        "GBP" => self::CURRENCY_ID_GBP,
        "TRY" => self::CURRENCY_ID_TRY,
        "PLN" => self::CURRENCY_ID_PLN,
        "CHF" => self::CURRENCY_ID_CHF,
        "RON" => self::CURRENCY_ID_RON,
        "BRL" => self::CURRENCY_ID_BRL,
        "ARS" => self::CURRENCY_ID_ARS,
        "UAH" => self::CURRENCY_ID_UAH,
        "PEN" => self::CURRENCY_ID_PEN
    ];

    /**
     * @param string $key
     * @return string
     */
    public static function getCurrencyNames(string $key): string
    {
        return ConfigManager::checkIfKeyExists($key, self::$currencyNames);
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getCurrencyIds(string $key): string
    {
        return ConfigManager::checkIfKeyExists($key, self::$currencyIds);
    }

}