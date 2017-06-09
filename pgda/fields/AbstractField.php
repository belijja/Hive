<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 09.06.2017
 * Time: 10:58
 */
declare(strict_types = 1);

namespace Pgda\Fields;

abstract class AbstractField
{
    private $typeLength;
    protected $name;
    protected $value;
    protected $invoke;
    protected $returnVariableName;

    const char = 'c';        //unsigned char
    const string = 'A';    //string or variable string
    const byte = 'c';        //unsigned byte
    const shortInt = 'n';    //2 Byte Int
    const int = 'N';        //4 Byte Int
    const bigint = 'NN';    //8 Byte Int

    protected static $c = 1;
    protected static $A = null;
    protected static $n = 2;
    protected static $N = 4;
    protected static $NN = 8;

    protected function setTypeLength($type, $length)
    {
        $this->typeLength = $type;
        if (empty($this->typeLength)) {
            $this->typeLength = $length;
        }
    }
}