<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;

class BasicCalculation implements IBasicOperation {
    public static function plus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        $number1 = Number::autoCorrectType($number1);
        $number2 = Number::autoCorrectType($number2);
        $number = $number1 + $number2;
        $number = number_format($number, $scale, ".", "");
        return $number;
    }

    public static function minus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        $number1 = Number::autoCorrectType($number1);
        $number2 = Number::autoCorrectType($number2);
        $number = $number1 - $number2;
        $number = number_format($number, $scale, ".", "");
        return $number;
    }

    public static function multiply($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        $number1 = Number::autoCorrectType($number1);
        $number2 = Number::autoCorrectType($number2);
        $number =  $number1 * $number2;
        $number = number_format($number, $scale, ".", "");
        return $number;
    }

    public static function divide($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        if($number2 == 0){
            throw new \Exception(Number::MSG_DIVISOR_ZERO);
        }
        $number1 = Number::autoCorrectType($number1);
        $number2 = Number::autoCorrectType($number2);
        $number = $number1 / $number2;
        $number = number_format($number, $scale, ".", "");
        return $number;
    }

    public static function maxDivisor(int $number1, int $number2) :string{
        if($number2 == 0)
        {
            return (string)$number1;
        }
        else
        {
            return self::maxDivisor($number2, ($number1 % $number2));
        }
    }
}