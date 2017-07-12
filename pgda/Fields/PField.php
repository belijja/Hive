<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 09.06.2017
 * Time: 11:40
 */
declare(strict_types = 1);

namespace Pgda\Fields;

class PField extends AbstractField
{
    protected function __construct(string $name, string $type, $value = null, int $length = null, string $returnVariable = null)
    {
        $class = new \ReflectionClass($this);
        $constants = $class->getConstants();
        if (array_search($type, $constants) === false) {
            throw new \BadMethodCallException("Invalid Type " . $type . "  in: " . __METHOD__ . " on line: " . __LINE__);
        }
        if (parent::string === $type && empty($length)) {
            throw new \BadMethodCallException('Length can not be empty for type string in: ' . __METHOD__ . " on line: " . __LINE__);
        }
        $this->name = str_pad($name, 30, " ", STR_PAD_RIGHT);
        $this->value = $value;
        if (!empty($length) && parent::string === $type) {
            $this->invoke = $type . $length;
        } else {
            $this->invoke = $type;
        }
        $this->setTypeLength($type, $length);
        $this->returnVariableName = $returnVariable;
    }

    public static function set(string $name, string $type, $value = null, int $length = null, string $returnVariable = null): PField//value must have different values
    {
        return new PField($name, $type, $value, $length, $returnVariable);
    }
}