<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\BaseClass;
use Feeler\Base\Math\MathConst;

class BasicOperation extends BaseClass {
    public static function plus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE, bool $asBigNumber = false){
        return $asBigNumber ? BasicBigCalculation::plus($number1, $number2, $scale) : BasicCalculation::plus($number1, $number2, $scale);
    }

    public static function minus($number1, $number2, int $scale = MathConst::DEFAULT_SCALE, bool $asBigNumber = false){
        return $asBigNumber ? BasicBigCalculation::minus($number1, $number2, $scale) : BasicCalculation::minus($number1, $number2, $scale);
    }

    public static function multiply($number1, $number2, int $scale = MathConst::DEFAULT_SCALE, bool $asBigNumber = false){
        return $asBigNumber ? BasicBigCalculation::multiply($number1, $number2, $scale) : BasicCalculation::multiply($number1, $number2, $scale);
    }

    public static function divide($number1, $number2, int $scale = MathConst::DEFAULT_SCALE, bool $asBigNumber = false){
        return $asBigNumber ? BasicBigCalculation::divide($number1, $number2, $scale) : BasicCalculation::divide($number1, $number2, $scale);
    }

    public static function randomInt($min, $max, bool $asBigNumber = false):string{
        if(!$asBigNumber){
            return random_int((int)$min, (int)$max);
        }
        return BasicGmp::instance()->randomInt((string)$min, (string)$max);
    }

    public static function maxDivisor($number1, $number2, bool $asBigNumber = false):string{
        return $asBigNumber ? BasicBigCalculation::maxDivisor($number1, $number2) : BasicCalculation::maxDivisor($number1, $number2);
    }
}