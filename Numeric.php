<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Numeric{
    public static function calc($number, $operand, $opt){
        if($opt === "+"){
            return gmp_add($number, $operand);
        }
        else if($opt === "-"){
            return gmp_sub($number, $operand);
        }
        else if($opt === "*"){
            return gmp_mul($number, $operand);
        }
        else if($opt === "/"){
            return gmp_div($number, $operand);
        }

        return "0";
    }
}