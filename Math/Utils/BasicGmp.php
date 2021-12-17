<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\Extension;
use Feeler\Base\Singleton;

class BasicGmp extends Singleton {
    public function __construct()
    {
        Extension::checkAvailability("gmp");
    }

    public static function init($number):\GMP{
        return ($number instanceof \GMP) ? $number : gmp_init($number);
    }

    public function randomInt(string $min, string $max):string{
        $min = gmp_init($min);
        $max = gmp_init($max);
        return gmp_strval(gmp_random_range($min, $max));
    }

    public static function isGmpObj($var):bool{
        return ($var instanceof \GMP);
    }

    public static function convertGmpObjToNumber($obj){
        return (self::isGmpObj($obj)) ? gmp_strval($obj) : $obj;
    }
}