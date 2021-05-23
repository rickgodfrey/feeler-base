<?php

namespace Feeler\Base;

class ByteFormat extends BaseClass {
    public static function convertStringToBinary(string $string): string{
        $array = preg_split("/(?<!^)(?!$)/u", $string);
        foreach($array as &$val){
            $temp = unpack("H*", $val);
            $val = base_convert($temp[1], 16, 2);
        }
        unset($val);
        return Arr::joinToString($array, " ");
    }

    public static function convertStringToHex(string $string): string{
        $array = preg_split("/(?<!^)(?!$)/u", $string);
        foreach($array as &$val){
            $temp = unpack("H*", $val);
            $val = pack("H", $temp[1]);
        }
        unset($val);
        return Arr::joinToString($array, " ");
    }

    public static function convertBinaryToString(string $string): string{
        $array = Str::splitToArrayByDelimiter($string, " ");
        foreach($array as &$val){
            $val = base_convert($val, 2, 16);
            $val = pack("H".strlen($val), $val);
        }
        unset($val);
        return Arr::joinToString($array);
    }

    public static function convertHexToString(string $string): string{
        $array = Str::splitToArrayByDelimiter($string, " ");
        foreach($array as &$val){
            $val = unpack("H*", $val);
        }
        unset($val);
        return Arr::joinToString($array);
    }

    public static function convertBinaryToHex(string $string): string{
        return self::convertStringToHex(self::convertBinaryToString($string));
    }

    public static function convertHexToBinary(string $string): string{
        return self::convertStringToBinary(self::convertHexToString($string));
    }
}