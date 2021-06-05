<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\BaseClass;
use Feeler\Base\Extension;
use Feeler\Base\Number;
use Feeler\Base\Singleton;

class BasicBigNumber extends BaseClass {
    public static function isOverFlow($number):bool{
        if(!Number::isNumeric($number)){
            throw new \Exception("Error number");
        }
        return ((bccomp((string)$number, (string)Number::intMax(), 100000)) === 1);
    }
}