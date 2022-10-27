<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
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