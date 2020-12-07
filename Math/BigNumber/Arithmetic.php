<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\BigNumber;

use Feeler\Base\BaseClass;
use Feeler\Base\Exceptions\InvalidValueException;
use Feeler\Base\IArithmetic;
use Feeler\Base\Number;

class Arithmetic extends BaseClass implements IArithmetic {
    public static function add($number1, $number2):string
    {
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new InvalidValueException("Invalid number param");
        }

        return gmp_add($number1, $number2);
    }

    public static function sub($number1, $number2):string
    {
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new InvalidValueException("Invalid number param");
        }

        return gmp_sub($number1, $number2);
    }

    public static function beMultiplied($number1, $number2):string
    {
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new InvalidValueException("Invalid number param");
        }

        return gmp_mul($number1, $number2);
    }

    public static function beDivided($number1, $number2):string
    {
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new InvalidValueException("Invalid number param");
        }

        return gmp_div($number1, $number2);
    }
}