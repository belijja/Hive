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
    public $typeLength;
    public $name;//these variables are added because there is no extending of stdClass due to dynamically adding variables
    public $value;
    public $invoke;
    public $returnVariableName;

    const char = 'c';        //unsigned char
    const string = 'A';    //string or variable string
    const byte = 'c';        //unsigned byte
    const shortInt = 'n';    //2 Byte Int
    const int = 'N';        //4 Byte Int
    const bigint = 'NN';    //8 Byte Int

    private static $c = 1;
    private static $A = null;
    private static $n = 2;
    private static $N = 4;
    private static $NN = 8;

    /**
     * @param string $type
     * @param int $length
     */
    protected function setTypeLength(string $type, int $length = null): void
    {
        $this->typeLength = self::$$type;
        if (empty($this->typeLength)) {
            $this->typeLength = $length;
        }
    }
}