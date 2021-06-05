<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\BaseClass;

interface IBasicOperation {
    public static function plus($number1, $number2, int $scale);
    public static function minus($number1, $number2, int $scale);
    public static function multiply($number1, $number2, int $scale);
    public static function divide($number1, $number2, int $scale);
}