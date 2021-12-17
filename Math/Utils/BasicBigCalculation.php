<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;

class BasicBigCalculation implements IBasicOperation {
    public static function plus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        return bcadd((string)$number1, (string)$number2, (int)$scale);
    }

    public static function minus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        return bcsub((string)$number1, (string)$number2, (int)$scale);
    }

    public static function multiply($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        return bcmul((string)$number1, (string)$number2, (int)$scale);
    }

    public static function divide($number1, $number2, int $scale = MathConst::DEFAULT_SCALE):string{
        if(!Number::isNumeric($number1) || !Number::isNumeric($number2)){
            throw new \Exception(Number::MSG_INITIALIZATION_FAILED);
        }
        if($number2 == 0){
            throw new \Exception(Number::MSG_DIVISOR_ZERO);
        }
        return bcdiv((string)$number1, (string)$number2, (int)$scale);
    }

    public static function decimalFormat($number, int $decimalPlaceLen = MathConst::DEFAULT_SCALE){
        return self::plus($number, "0", $decimalPlaceLen);
    }

    public static function maxDivisor($number1, $number2) :string{
        $number1 = BasicGmp::convertGmpObjToNumber($number1);
        $number2 = BasicGmp::convertGmpObjToNumber($number2);

        if($number2 == 0)
        {
            if(Number::isNumeric($number1)){
                $number = $number1;
            }
            else{
                $number = "0";
            }
        }
        else
        {
            $number1 = BasicGmp::init($number1);
            $number2 = BasicGmp::init($number2);
            $number = self::maxDivisor($number2, ($number1 % $number2));
            $number = BasicGmp::convertGmpObjToNumber($number);
        }

        $number = BasicGmp::convertGmpObjToNumber($number);

        return $number;
    }
}