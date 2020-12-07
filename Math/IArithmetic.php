<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

interface IArithmetic {
    public static function add($number1, $number2):string;
    public static function sub($number1, $number2):string;
    public static function beMultiplied($number1, $number2):string;
    public static function beDivided($number1, $number2):string;
}