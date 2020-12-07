<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class NumberFormat extends BaseClass {
    public static function isBinary($string):bool{
        if(!Str::isAvailable($string)){
            return false;
        }

        return preg_match("/[01]+/", $string);
    }

    public static function isOctal($string):bool{
        if(!Str::isAvailable($string)){
            return false;
        }

        return preg_match("/[0-7]+/", $string);
    }

    public static function isDecimal($string):bool{
        return Number::isNumeric($string);
    }

    public static function isHex($string):bool{
        if(!Str::isAvailable($string)){
            return false;
        }

        return preg_match("/0[xX][0-9abcdefABCDEF]+/", $string);
    }

    public static function act($number, $decimalPlaceLen = 2, $round = true, $fixedDecimalPlace = false, $showThousandsSep = false){
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

    public static function convert($number, int $fromFormat, int $toFormat):string{
        return base_convert($number, $fromFormat, $toFormat);
    }

    public static function convertDecimalToBinary($number):string{
        if(!Number::isNumeric($number)){
            return false;
        }

        return self::convert($number, 10, 2);
    }

    public static function convertDecimalToOctal($number):string{
        if(!Number::isNumeric($number)){
            return false;
        }

        return self::convert($number, 10, 8);
    }

    public static function convertDecimalToHex($number):string{
        if(!Number::isNumeric($number)){
            return false;
        }

        return self::convert($number, 10, 16);
    }

    public static function convertBinaryToOctal($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 2, 8);
    }

    public static function convertBinaryToDecimal($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 2, 10);
    }

    public static function convertBinaryToHex($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 2, 16);
    }

    public static function convertOctalToBinary($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 8, 2);
    }

    public static function convertOctalToDecimal($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 8, 10);
    }

    public static function convertOctalToHex($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 8, 16);
    }

    public static function convertHexToBinary($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 16, 2);
    }

    public static function convertHexToOctal($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 16, 8);
    }

    public static function convertHexToDecimal($number):string{
        if(!Str::isAvailable($number)){
            return false;
        }

        return self::convert($number, 16, 10);
    }
}