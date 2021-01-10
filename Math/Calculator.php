<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math;

use Feeler\Base\BaseClass;
use Feeler\Base\Math\RPN\Expression;

class Calculator extends BaseClass {
    public static function calc(string $expression, bool $asBigNumber = false):string{
        return (string)Expression::instance()->execute($expression,  $asBigNumber);
    }
}