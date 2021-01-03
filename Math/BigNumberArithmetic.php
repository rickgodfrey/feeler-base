<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math;

use Feeler\Base\BaseClass;
use Feeler\Base\Math\RPN\Calculator;

class BigNumberArithmetic extends BaseClass {
    public static function calc(string $pattern):string{
        return (string)Calculator::getInstance()->executeExpression($pattern);
    }
}