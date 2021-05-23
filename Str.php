<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Str extends BaseClass {
    const CASE_UPPER = "case_upper";
    const CASE_LOWER = "case_lower";
    const CASE_MIXED = "case_mixed";

    const IS_LETTER = "is_letter";
    const IS_NON_LETTER = "is_non_letter";

    const ENCODING_ASCII = "ascii";
    const ENCODING_UTF8 = "utf-8";
    const ENCODING_UNKNOWN = "encoding_unknown";

    const REPEAT_TIMES_AUTO = "repeat_times_auto";

    const SUPPORTED_ENCODINGS = ["utf-8", "ascii", "utf-32", "utf-32be", "utf-32le", "utf-16", "utf-16be", "utf-16le", "utf-7", "utf7-imap", "utf-8-mobile", "ucs-4", "ucs-4be", "ucs-4le", "ucs-2", "ucs-2be", "ucs-2le", "gb18030", "koi8-r", "koi8-u", "euc-cn", "euc-kr", "euc-jp", "euc-tw", "eucjp-win", "sjis", "sjis-win", "sjis-mac", "sjis-mobile", "jis", "jis-ms", "cp866", "cp932", "cp936", "cp950", "cp51932", "cp50220", "cp50220raw", "cp50221", "cp50222", "iso-8859-1", "iso-8859-2", "iso-8859-3", "iso-8859-4", "iso-8859-5", "iso-8859-6", "iso-8859-7", "iso-8859-8", "iso-8859-9", "iso-8859-10", "iso-8859-13", "iso-8859-14", "iso-8859-15", "iso-8859-16", "iso-2022-kr", "iso-2022-jp", "iso-2022-jp-ms", "iso-2022-jp-mobile", "byte2be", "byte2le", "byte4be", "byte4le", "7bit", "8bit", "base64", "html-entries", "hz", "big-5", "uhc", "windows-1251", "windows-1252", "armscii-8"];
    const CASE_MINUSES = [0, 538976288, 2105376, 8224, 32, 536870944, 536870912, 538968064, 538976256, 2097152, 2105344, 8192];
    const UPPER_LETTERS_DICT = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    const LOWER_LETTERS_DICT = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];

    public static function isString($string): bool{
        return is_string($string);
    }

    public static function isAvailable(&$string) : bool{
        if(!self::isString($string)){return false;}
        $string = trim($string);
        if(!$string){return false;}
        return true;
    }

    public static function supportedEncodings(): array{
        return self::SUPPORTED_ENCODINGS;
    }

    public static function upperLettersDict(): array{
        return self::UPPER_LETTERS_DICT;
    }

    public static function lowerLettersDict(): array{
        return self::LOWER_LETTERS_DICT;
    }

    public static function isLetter(string $string): bool{
        return preg_match("/[a-zA-Z]/", $string) ? true : false;
    }

    public static function isLetters(string $string): bool{
        return preg_match("/[a-zA-Z]+/", $string) ? true : false;
    }

    public static function isUpperLetter(string $string): bool{
        return preg_match("/[A-Z]/", $string) ? true : false;
    }

    public static function isUpperLetters(string $string): bool{
        return preg_match("/[A-Z]+/", $string) ? true : false;
    }

    public static function isLowerLetter(string $string): bool{
        return preg_match("/[a-z]/", $string) ? true : false;
    }

    public static function isLowerLetters(string $string): bool{
        return preg_match("/[a-z]+/", $string) ? true : false;
    }

    public static function detectEncoding(string $string): string{
        $encoding = mb_detect_encoding($string, self::supportedEncodings());
        return $encoding ? $encoding : self::ENCODING_UNKNOWN;
    }

    public static function getChar(string $string, int $position = 0): string{
        if(!self::isAvailable($string)){
            return false;
        }
        return mb_substr($string, $position, 1);
    }

    public static function slice(string $string, int $position = 0, int $length = -1): string{
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
        if(!($char = self::getFirstChar($string))){
            return "";
        }

        if(self::isLetter($char)){
            return ($case === self::CASE_UPPER) ? strtoupper($char) : strtolower($char);
        }

        if(self::isZhChar($char)){
            return ($case === self::CASE_UPPER) ? strtoupper(self::getZhLetter($char)) : strtolower(self::getZhLetter($char));
        }

        return "";
    }

    public static function getLastLetter(string $string, $case = self::CASE_UPPER): string{
        if(!($char = self::getLastChar($string))){
            return "";
        }

        if(self::isLetter($char)){
            return ($case === self::CASE_UPPER) ? strtoupper($char) : strtolower($char);
        }

        if(self::isZhChar($char)){
            return ($case === self::CASE_UPPER) ? strtoupper(self::getZhLetter($char)) : strtolower(self::getZhLetter($char));
        }

        return "";
    }

    public static function isZhChar(string $string): bool{
        return preg_match("/[\x{4e00}-\x{9fa5}]/u", $string) ? true : false;
    }

    public static function isZhString(string $string, bool $strict = true): bool{
        if($strict){
            return preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $string) ? true : false;
        }
        else{
            return preg_match("/[\x{4e00}-\x{9fa5}]+/u", $string) ? true : false;
        }
    }

    public static function hasZhString(string $string): bool{
        return self::isZhString($string, false);
    }

    public static function getZhLetter(string $string, int $posititon = 0, string $case = self::CASE_UPPER): string{
        if(!self::isAvailable($string)){
            return "";
        }

        if(self::detectEncoding($string) === self::ENCODING_ASCII){
            $letter = ($letter = self::getChar($string, $posititon)) && self::isLetters($letter) ? $letter : false;
            if($letter){
                return $case === self::CASE_UPPER ? strtoupper($letter) : $letter;
            }

            return "";
        }

        $char = self::getChar($string, $posititon);
        if(!$char){
            return "";
        }

        $char = iconv(self::ENCODING_UTF8,"gb2312", $char);

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

    public static function hideParts($string, $hideRate = 0.5, $symbol = "*", $symbolRepeatTimes = self::REPEAT_TIMES_AUTO){
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

        if($symbolRepeatTimes === self::REPEAT_TIMES_AUTO){
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
        }
        else{
            $leftPlace = ceil($showLen / 2);
        }

        $stringParts = [
            mb_substr($string, 0, $leftPlace),
            mb_substr($string, $leftPlace, $hideLen),
            mb_substr($string, ($leftPlace + $hideLen))
        ];

        return $stringParts[0].str_repeat($symbol, $symbolRepeatTimes).$stringParts[2];
    }

    public static function lettersAggr($string) : array{
        if(!self::isAvailable($string)){
            return [];
        }

        $letters = [];
        $nonLetters = [];
        $charsPositions = [];
        $string = mb_strtolower($string);
        $chars = str_split($string);
        $charPosition = 0;
        foreach($chars as $char){
            $charCode = (int)(hexdec(bin2hex($char)));

            if($charCode >= 97 && $charCode <= 122){
                $letters[] = $char;
                $charsPositions[$charPosition] = self::IS_LETTER;
            }
            else{
                $nonLetters[] = $char;
                $charsPositions[$charPosition] = self::IS_NON_LETTER;
            }

            $charPosition++;
        }

        if(!$letters){
            return [];
        }

        $minuses = self::CASE_MINUSES;
        $lettersSegsCount = 0;

        foreach($letters as $key => $letter) {
            unset($letters[$key]);

            $index = ceil($rs = ($key + 1) / 4) - 1;
            if(!isset($letters[$index])){
                $letters[$index] = "";
                $lettersSegsCount++;
            }

            $letters[$index] .= $letter;
        }

        $letters = Arr::tidy($letters);

        for($i = 0; $i < $lettersSegsCount; $i++){
            $lettersSeg = $letters[$i];
            $lettersSegSum = (int)base_convert(bin2hex($lettersSeg), 16, 10);
            $letters[$i] = [];

            foreach($minuses as $minus){
                $letters[$i][] = hex2bin(base_convert((string)($lettersSegSum - $minus), 10, 16));
            }
        }

        $array = [];
        for($i = 0; $i < $lettersSegsCount; $i++){
            if($i === 0){
                $array = $letters[0];
                continue;
            }

            $rs = [];
            foreach($array as $lettersSegAggr){
                foreach($letters[$i] as $lettersSeg){
                    $rs[] = $lettersSegAggr.$lettersSeg;
                }
            }

            $array = $rs;
        }

        foreach($array as &$chars){
            $chars = str_split($chars);
            $string = "";

            foreach($charsPositions as $charPosition => $charType){
                if($charType === self::IS_LETTER){
                    $string .= Arr::current($chars);
                    next($chars);
                }
                else{
                    $string .= Arr::current($nonLetters);
                    next($nonLetters);
                }
            }

            reset($nonLetters);
            $chars = $string;
        }
        unset($chars);

        return $array;
    }

    public static function splitToArrayByUnitLength(string $string, int $unitLength = 1, int $limit = -1):array{
        if(!Str::isAvailable($string) || !Number::isUnsignedInt($unitLength) || (!Number::isUnsignedInt($limit) && $limit !== -1)){
            return [];
        }
        if($limit !== -1){
            $string = mb_substr($string, 0, $limit);
        }
        return str_split($string, $unitLength);
    }

    public static function splitToArrayByDelimiter(string $string, string $delimiter, int $limit = -1):array{
        if(!Str::isAvailable($string) || !Str::isString($delimiter) || (!Number::isUnsignedInt($limit) && $limit !== -1)){
            return [];
        }
        if($limit !== -1){
            $string = mb_substr($string, 0, $limit);
        }
        if($delimiter === ""){
            return str_split($string);
        }
        return explode($delimiter, $string);
    }

    public static function split(string $string, int $unitLength = 1, string $delimiter = " ", int $limit = -1):string{
        return Arr::joinToString(self::splitToArray($string, $unitLength, $limit), $delimiter);
    }

    public static function replace(string $find, string $replacement, string $string, bool $ignoreCase = false):int{
        ($ignoreCase and str_ireplace($find, $replacement, $string, $count)) or str_replace($find, $replacement, $string, $count);
        return $count;
    }
}