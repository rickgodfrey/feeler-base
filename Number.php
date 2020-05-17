<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Number extends BaseClass {
    public static function isNumber($number){
        if(!is_int($number) && !is_float($number)){
            return false;
        }

        return true;
    }

    public static function isInteric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, ".") !== false){
            return false;
        }

        return true;
    }

    public static function isFloaric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, ".") === false){
            return false;
        }

        return true;
    }

    public static function isInt($number){
        return is_int($number) ? true : false;
    }

    public static function isFloat($number){
        return is_float($number) ? true : false;
    }

    public static function isUnsignedNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedInt($number){
        if(!self::isInt($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number !== 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number !== 0){
            return false;
        }

        return true;
    }

    public static function isNumeric(&$number){
        return is_numeric($number) ? true : (Str::isAvailable($number) ? is_numeric($number) : false);
    }

    public static function isMinusNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        return self::isUnsignedNumeric($number) ? false : true;
    }

    public function isMinusInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        return self::isUnsignedInteric($number) ? false : true;
    }

    public function isMinusFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        return self::isUnsignedFloaric($number) ? false : true;
    }

    public static function isMinusInt($number){
        if(!self::isInt($number)){
            return false;
        }

        return self::isUnsignedInt($number) ? false : true;
    }

    public static function isMinusFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        return self::isUnsignedFloat($number) ? false : true;
    }

    public static function isPosiNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiInt($number){
        if(!self::isInt($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function abs($number){
        if(!self::isNumeric($number)){
            return 0;
        }

        $number = abs($number);

        if(!self::isNumeric($number)){
            return 0;
        }

        return $number;
    }

    public static function format($number, $decimalPlaceLen = 2, $round = true, $fixedDecimalPlace = false, $showThousandsSep = false){
        if(!self::isNumeric($number) || $number == 0 || !self::isInt($decimalPlaceLen) || $decimalPlaceLen < 0){
            if($fixedDecimalPlace && self::isPosiInteric($decimalPlaceLen)){
                return "0.".str_repeat("0", $decimalPlaceLen);
            }
            else{
                return 0;
            }
        }

        if($showThousandsSep){
            $thousandsSep = ",";
        }
        else{
            $thousandsSep = "";
        }

        if($round){
            $number = sprintf("%.{$decimalPlaceLen}f", number_format($number, $decimalPlaceLen, ".", $thousandsSep));
        }
        else{
            if($decimalPlaceLen == 0){
                $number = floor($number);
            }
            else{
                $digit = $decimalPlaceLen + 1;
                $number = sprintf("%.{$digit}f", number_format($number, $digit, ".", $thousandsSep));
                if(self::isFloaric($number)){
                    $numberParts = explode(".", $number);
                    $decimalLen = strlen($numberParts[1]);
                    if($decimalLen > $decimalPlaceLen){
                        $numberParts[1] = substr($numberParts[1], 0, ($decimalLen - ($decimalLen - $decimalPlaceLen)));
                        $number = $numberParts[0].".".$numberParts[1];
                    }
                }
            }
        }

        if($fixedDecimalPlace && self::isPosiInteric($decimalPlaceLen)){
            $numberParts = explode(".", (string)$number, 2);

            if(isset($numberParts[1])){
                if(($len = strlen($numberParts[1])) < $decimalPlaceLen){
                    $difference = $decimalPlaceLen - $len;
                    $number = $numberParts[0].".{$numberParts[1]}".str_repeat("0", $difference);
                }
            }
            else{
                $number = $number.".".str_repeat("0", $decimalPlaceLen);
            }
        }
        else{
            $number = (float)$number;
        }

        return $number;
    }
}