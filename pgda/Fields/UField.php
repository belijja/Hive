<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 09.06.2017
 * Time: 11:19
 */
declare(strict_types = 1);

namespace Pgda\Fields;

class UField extends AbstractField
{

    protected function __construct(string $name, string $type, string $returnVariable = null, int $length = null)
    {

        $class = new \ReflectionClass($this);
        $constants = $class->getConstants();

        if (array_search($type, $constants) === false) {
            throw new \BadMethodCallException("Invalid Type '$type'  in: " . __METHOD__ . " in line: " . __LINE__);
        }
        if (parent::string === $type && empty($length)) {
            throw new \BadMethodCallException('Lenght CAN NOT BE EMPTY for Type String in: ' . __METHOD__ . " in line: " . __LINE__);
        }
        $this->name = str_pad($name, 30, " ", STR_PAD_RIGHT);
        if (!empty($length) && parent::string === $type) {
            $this->invoke = $type . $length;
        } else {
            $this->invoke = $type;
        }
        $this->setTypeLength($type, $length);
        $this->returnVariableName = $returnVariable;

    }

    public static function set(string $name, string $type, string $returnVariable = null, int $length = null): UField
    {
        return new UField($name, $type, $returnVariable, $length);
    }
}