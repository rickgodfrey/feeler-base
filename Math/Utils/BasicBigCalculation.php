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
}