<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 21.04.2017
 * Time: 15:13
 */
declare(strict_types = 1);

namespace Helpers\ParamHelpers;

/**
 * Class ParamManager
 * @package Helpers\ParamHelpers
 */
class ParamManager
{
    /**
     * @param $paramName
     * @return mixed
     * @throws \SoapFault
     */
    public function mandatoryParam(string $paramName): string
    {
        if (isset($_POST[$paramName])) {
            return $_POST[$paramName];
        } else {
            throw new \SoapFault('MISSING_ARG', "Required argument " . $paramName . " missing in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
    }

    /**
     * @param $paramName
     * @return int
     * @throws \SoapFault
     */
    public function mandatoryParamInt(string $paramName): int
    {
        if (isset($_POST[$paramName]) && !ctype_digit($_POST[$paramName])) {
            throw new \SoapFault('INVALID_ARG', "Argument " . $paramName . " not in correct format in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        return (int)$this->mandatoryParam($paramName);
    }

    /**
     * @param $paramName
     * @return float
     * @throws \SoapFault
     */
    public function mandatoryParamFloat(string $paramName): float
    {
        if (isset($_POST[$paramName]) && !is_numeric($_POST[$paramName])) {
            throw new \SoapFault('INVALID_ARG', "Argument " . $paramName . " not in correct format in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        return (float)$this->mandatoryParam($paramName);
    }

    /**
     * @param string $paramName
     * @param string|null $def
     * @return string
     */
    public function optionalParam(string $paramName, string $def = null): string
    {
        if ($def == null) {
            return isset($_POST[$paramName]) ? $_POST[$paramName] : "0";
        } else {
            if (isset($_POST[$paramName])) {
                return $_POST[$paramName];
            } else {
                return $def;
            }
        }
    }

    /**
     * @param string $paramName
     * @param int|null $def
     * @return int
     * @throws \SoapFault
     */
    public function optionalParamInt(string $paramName, int $def = null): int
    {
        if (isset($_POST[$paramName]) && !ctype_digit($_POST[$paramName])) {
            throw new \SoapFault('INVALID_ARG', "Argument " . $paramName . " not in correct format in class " . __CLASS__ . " and method " . __METHOD__ . " and line " . __LINE__);
        }
        return (int)$this->optionalParam($paramName, $def);
    }
}