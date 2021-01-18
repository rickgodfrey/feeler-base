<?php

namespace Feeler\Base;

class DataFormat extends BaseClass {
    public static function convertStringToBinary(string $string, string $delimiter = " "): string{
        $array = preg_split("/(?<!^)(?!$)/u", $string);
        foreach($array as &$val){
            $temp = unpack("H*", $val);
            $val = base_convert($temp[1], 16, 2);
        }
        return Arr::join($delimiter, $array);
    }

    public static function convertStringToHex(string $string, string $delimiter = " "): string{
        $array = preg_split("/(?<!^)(?!$)/u", $string);
        foreach($array as &$val){
            $val = unpack("H*", $val);
            $val = $val[1];
        }
        unset($val);
        return Arr::join($delimiter, $array);
    }

    public static function convertBinaryToString(string $string, string $delimiter = " "): string{
        $array = Str::join($delimiter, $string);
        foreach($array as &$val){
            $val = pack("H".strlen(base_convert($val, 2, 16)), base_convert($val, 2, 16));
        }
        unset($val);
        return Arr::join("", $array);
    }

    public static function convertHexToString(string $string, string $delimiter = " "): string{
        $array = Str::join($delimiter, $string);
        foreach($array as &$val){
            $val = pack("H".strlen($val), $val);
        }
        unset($val);
        return Arr::join("", $array);
    }

    public static function convertBinaryToHex(string $string, string $delimiter = " "): string{
        return self::convertStringToHex(self::convertBinaryToString($string, $delimiter), $delimiter);
    }

    public static function convertHexToBinary(string $string, string $delimiter = " "): string{
        return self::convertStringToBinary(self::convertHexToString($string, $delimiter), $delimiter);
    }
}