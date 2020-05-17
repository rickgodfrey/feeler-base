<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Str extends BaseClass {
    const CASE_UPPER = 1;
    const CASE_LOWER = 2;
    const FIRST = 1;
    const LAST = 2;

    public static function isAvailable(&$string): bool{
        if(!is_string($string)){
            return false;
        }

        $string = trim($string);

        if(!$string){
            return false;
        }

        return true;
    }

    public static function isString($string): bool{
        return is_string($string);
    }

    public static function supportedEncodings(): array{
        return [
            "UTF-8",
            "ASCII",
            "UTF-32",
            "UTF-32BE",
            "UTF-32LE",
            "UTF-16",
            "UTF-16BE",
            "UTF-16LE",
            "UTF-7",
            "UTF7-IMAP",
            "UTF-8-Mobile",
            "UCS-4",
            "UCS-4BE",
            "UCS-4LE",
            "UCS-2",
            "UCS-2BE",
            "UCS-2LE",
            "GB18030",
            "KOI8-R",
            "KOI8-U",
            "EUC-CN",
            "EUC-KR",
            "EUC-JP",
            "EUC-TW",
            "eucJP-win",
            "SJIS",
            "SJIS-win",
            "SJIS-mac",
            "SJIS-Mobile",
            "JIS",
            "JIS-ms",
            "CP866",
            "CP932",
            "CP936",
            "CP950",
            "CP51932",
            "CP50220",
            "CP50220raw",
            "CP50221",
            "CP50222",
            "ISO-8859-1",
            "ISO-8859-2",
            "ISO-8859-3",
            "ISO-8859-4",
            "ISO-8859-5",
            "ISO-8859-6",
            "ISO-8859-7",
            "ISO-8859-8",
            "ISO-8859-9",
            "ISO-8859-10",
            "ISO-8859-13",
            "ISO-8859-14",
            "ISO-8859-15",
            "ISO-8859-16",
            "ISO-2022-KR",
            "ISO-2022-JP",
            "ISO-2022-JP-MS",
            "ISO-2022-JP-MOBILE",
            "byte2be",
            "byte2le",
            "byte4be",
            "byte4le",
            "7bit",
            "8bit",
            "BASE64",
            "HTML-ENTITIES",
            "HZ",
            "BIG-5",
            "UHC",
            "Windows-1251",
            "Windows-1252",
            "ArmSCII-8",
        ];
    }

    public static function upperLettersDict(): array{
        return ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    }

    public static function lowerLettersDict(): array{
        return ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];
    }

    public static function isLetter(string $string): bool{
        return preg_match("/[a-z]/i", $string);
    }

    public static function isUpperLetter(string $string): bool{
        return preg_match("/[A-Z]/", $string);
    }

    public static function isLowerLetter(string $string): bool{
        return preg_match("/[a-z]/", $string);
    }

    public static function isPureLetters(string $string): bool{
        return preg_match("/[a-z\s]+/i", $string);
    }

    public static function detectEncoding(string $string): string{
        $encoding = mb_detect_encoding($string, self::supportedEncodings());

        return $encoding ? $encoding : "UNKNOWN";
    }

    public static function getChar(string $string, int $position = 0): string{
        if(!self::isAvailable($string)){
            return false;
        }

        return mb_substr($string, $position, 1);
    }

    public static function cutOut(string $string, int $position = 0, int $length = -1): string{
        if(!self::isAvailable($string) || $length < -1 || $length === 0){
            return false;
        }

        if($length === -1){
            $length = null;
        }

        return mb_substr($string, $position, $length);
    }

    public static function getFirstChar(string $string): string{
        return self::getChar($string, 0);
    }

    public static function getLastChar(string $string): string{
        return self::getChar($string, -1);
    }

    public static function getFirstLetter(string $string, $case = self::CASE_UPPER): string{
        $char = self::getFirstChar($string);

        if(self::isLetter($char)){
            return $case === self::CASE_UPPER ? strtoupper($char) : strtolower($char);
        }

        return "";
    }

    public static function getLastLetter(string $string, $case = self::CASE_UPPER): string{
        $char = self::getLastChar($string);

        if(self::isLetter($char)){
            return $case === self::CASE_UPPER ? strtoupper($char) : strtolower($char);
        }

        return "";
    }

    public static function isZhChar(string $string): bool{
        return preg_match("/[\x{4e00}-\x{9fa5}]/u", $string);
    }

    public static function isZhString(string $string, bool $strict = true): bool{
        if($strict){
            return preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $string);
        }
        else{
            return preg_match("/[\x{4e00}-\x{9fa5}]+/u", $string);
        }
    }

    public static function hasZhString(string $string): bool{
        return self::isZhString($string, false);
    }

    public static function getZhLetter(string $string, int $posititon = 0, $case = self::CASE_UPPER): string{
        if(!self::isAvailable($string)){
            return false;
        }

        if(self::detectEncoding($string) == "ASCII"){
            $letter = ($letter = self::getChar($string, $posititon)) && self::isLetter($letter) ? $letter : false;
            if($letter){
                return $case === self::CASE_UPPER ? strtoupper($letter) : $letter;
            }

            return "";
        }

        $char = self::getChar($string, $posititon);
        if(!$char){
            return false;
        }

        $char = iconv("UTF-8","gb2312", $char);

        if (preg_match("/[\x7f-\xff]/", $char))
        {
            $asciiCode = ord($char[0]) * 256 + ord($char[1]) - 65536;

            if ($asciiCode >= -20319 and $asciiCode <= -20284)
                $letter = "A";
            else if ($asciiCode >= -20283 and $asciiCode <= -19776)
                $letter = "B";
            else if ($asciiCode >= -19775 and $asciiCode <= -19219)
                $letter = "C";
            else if ($asciiCode >= -19218 and $asciiCode <= -18711)
                $letter = "D";
            else if ($asciiCode >= -18710 and $asciiCode <= -18527)
                $letter = "E";
            else if ($asciiCode >= -18526 and $asciiCode <= -18240)
                $letter = "F";
            else if ($asciiCode >= -18239 and $asciiCode <= -17923)
                $letter = "G";
            else if ($asciiCode >= -17922 and $asciiCode <= -17418)
                $letter = "H";
            else if ($asciiCode >= -17417 and $asciiCode <= -16475)
                $letter = "J";
            else if ($asciiCode >= -16474 and $asciiCode <= -16213)
                $letter = "K";
            else if ($asciiCode >= -16212 and $asciiCode <= -15641)
                $letter = "L";
            else if ($asciiCode >= -15640 and $asciiCode <= -15166)
                $letter = "M";
            else if ($asciiCode >= -15165 and $asciiCode <= -14923)
                $letter = "N";
            else if ($asciiCode >= -14922 and $asciiCode <= -14915)
                $letter = "O";
            else if ($asciiCode >= -14914 and $asciiCode <= -14631)
                $letter = "P";
            else if ($asciiCode >= -14630 and $asciiCode <= -14150)
                $letter = "Q";
            else if ($asciiCode >= -14149 and $asciiCode <= -14091)
                $letter = "R";
            else if ($asciiCode >= -14090 and $asciiCode <= -13319)
                $letter = "S";
            else if ($asciiCode >= -13318 and $asciiCode <= -12839)
                $letter = "T";
            else if ($asciiCode >= -12838 and $asciiCode <= -12557)
                $letter = "W";
            else if ($asciiCode >= -12556 and $asciiCode <= -11848)
                $letter = "X";
            else if ($asciiCode >= -11847 and $asciiCode <= -11056)
                $letter = "Y";
            else if ($asciiCode >= -11055 and $asciiCode <= -10247)
                $letter = "Z";

            return $case === self::CASE_UPPER ? $letter : strtolower($letter);
        }
        else
        {
            return "";
        }
    }

    public static function getZhFirstLetter(string $string, $case = self::CASE_UPPER): string{
        return self::getZhLetter($string, 0, $case);
    }

    public static function getZhLastLetter(string $string, $case = self::CASE_UPPER): string{
        return self::getZhLetter($string, -1, $case);
    }

    public static function hideParts($string, $hideRate = 0.5, $symbol = "*", $symbolRepeatTimes = 4){
        if(!self::isAvailable($string) || !is_float($hideRate) || $hideRate <= 0){
            return "";
        }

        if($hideRate >= 1){
            return $string;
        }

        $strLen = mb_strlen($string);

        if($strLen == 1){
            return $symbol;
        }

        $hideLen = ceil($strLen * $hideRate);
        $showLen = $strLen - $hideLen;

        if($symbolRepeatTimes == "AUTO"){
            $symbolRepeatTimes = $hideLen;
        }
        else if(!Number::isInteric($symbolRepeatTimes)){
            $symbolRepeatTimes = 4;
        }

        if($symbolRepeatTimes > $hideLen){
            $symbolRepeatTimes = $hideLen;
        }

        if($symbolRepeatTimes < 1){
            $symbolRepeatTimes = 1;
        }

        if($strLen == 2){
            $leftPlace = 0;
            $rightPlace = 1;
        }
        else{
            $leftPlace = ceil($showLen / 2);
            $rightPlace = $showLen - $leftPlace;
        }

        $stringParts = [
            mb_substr($string, 0, $leftPlace),
            mb_substr($string, $leftPlace, $hideLen),
            mb_substr($string, ($leftPlace + $hideLen))
        ];

        return $stringParts[0].str_repeat($symbol, $symbolRepeatTimes).$stringParts[2];
    }

    public static function utf8Encode($string){
        if(!self::isAvailable($string)){
            return "";
        }

        $string = utf8_encode($string);
        $string = utf8_decode($string);

        return $string;
    }

    public static function mbSplit($string, $len = 1) {
        $start = 0;
        $strlen = mb_strlen($string);
        $array = [];

        while ($strlen){
            $array[] = mb_substr($string, $start, $len,"utf8");
            $string = mb_substr($string, $len, $strlen,"utf8");
            $strlen = mb_strlen($string);
        }

        return $array;
    }
}