<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\Extension;
use Feeler\Base\Number;
use Feeler\Base\Singleton;

class BasicGmp extends Singleton {
    public function __construct()
    {
        Extension::checkAvailability("gmp");
    }

    public static function isGmpObj($number):bool{
        return ($number instanceof \GMP);
    }

    public static function plus($number1, $number2):string{
        $number1 = gmp_init($number1);
        $number2 = gmp_init($number2);
        return gmp_strval($number1 + $number2);
    }

    public static function sub($number1, $number2):string{
        $number1 = gmp_init($number1);
        $number2 = gmp_init($number2);
        return gmp_strval($number1 - $number2);
    }

    public static function multiply($number1, $number2):string{
        $number1 = gmp_init($number1);
        $number2 = gmp_init($number2);
        return gmp_strval($number1 * $number2);
    }

    public static function divide($number1, $number2):string{
        $number1 = gmp_init($number1);
        $number2 = gmp_init($number2);
        return gmp_strval($number1 / $number2);
    }

    public function randomInt(string $min, string $max):string{
        $min = gmp_init($min);
        $max = gmp_init($max);
        return gmp_strval(gmp_random_range($min, $max));
    }

    public static function convertGmpObjToNumber($obj){
        return (self::isGmpObj($obj)) ? gmp_strval($obj) : $obj;
    }
}