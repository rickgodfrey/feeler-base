<?php

namespace Feeler\Base;

class DataFormat extends BaseClass {
    public static function convertStringToBinary(string $string): string{
        return pack("H32", str_replace("-", "", $string));
    }

    public static function convertBinaryToString(string $string): string{
        return implode("-", unpack("H8a/H4b/H4c/H4d/H12e", $string));
    }

    public static function convertBinaryToHex(string $string):string{
        return bin2hex($string);
    }
}