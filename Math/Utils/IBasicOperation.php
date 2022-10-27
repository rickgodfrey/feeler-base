<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base\Math\Utils;

use Feeler\Base\BaseClass;

interface IBasicOperation {
    public static function plus($number1, $number2, int $scale);
    public static function minus($number1, $number2, int $scale);
    public static function multiply($number1, $number2, int $scale);
    public static function divide($number1, $number2, int $scale);
}