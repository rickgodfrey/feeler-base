<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

class NumberFormat extends BaseClass {
    public static function isBinary($number):bool{
        if(!Str::isAvailable($number)){
            return false;
        }
        if(strlen($number) > 4 && strpos($number, " ") !== false){
            return preg_match("/^(?:[01]{4})+?(?:\s[01]{4})*$/", $number) ? true : false;
        }
        else{
            return preg_match("/^[01]+$/", $number) ? true : false;
        }
    }

    public static function isOctal($number):bool{
        if(!Number::isNumeric($number) || !($number = (string)$number) || strpos($number, "0") !== 0){
            return false;
        }
        return ctype_digit($number);
    }

    public static function isDecimal($string):bool{
        return Number::isNumeric($string);
    }

    public static function isHex($number):bool{
        if(!Number::isNumeric($number) || !($number = (string)$number) || strpos($number, "0") !== 0){
            return false;
        }
        return !ctype_digit($number);
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