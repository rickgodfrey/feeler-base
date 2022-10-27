<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

class Arr extends BaseClass {
    const SORT_ASC = SORT_ASC;
    const SORT_DESC = SORT_DESC;
    const SORT_NATURAL = SORT_NATURAL;
    const SORT_NUMERIC = SORT_NUMERIC;
    const SORT_STRING = SORT_STRING;
    const SORT_REGULAR = SORT_REGULAR;
    const SORT_LOCALE_STRING = SORT_LOCALE_STRING;

    const VAL_COMPLETE_REGEX = "/^(?:\(([^\(\)\:]*)(?:\:([^\(\)\:]*)?)?\))?\{\{([^\{\}]*)\}\}$/";

    public function __isset($name)
    {
        return null;
    }

    public static function isAssoc($value) :bool {
        return is_array($value) && array_keys($value) !== range(0, count($value) - 1);
    }

    public static function isArray($value, $strict = false) :bool {
        if(!$strict){
            return is_array($value);
        }
        else{
            return is_array($value) && !self::isAssoc($value);
        }
    }

    //Array of keys to delete and restore to incrementing key
    public static function tidy($array = []): array{
        if(!self::isAvailable($array)){
            return [];
        }

        $array = array_values($array);

        return $array;
    }

    public static function inArray($value, array $array, bool $strict = false) : bool{
        return in_array($value, $array, $strict);
    }

    public static function sort(&$array, int $order = self::SORT_ASC, int $type = self::SORT_NATURAL, bool $keepKey = true): bool{
        if(!self::isAvailable($array)){
            return false;
        }

        if($type === self::SORT_NATURAL){
            if($order === self::SORT_ASC){
                natsort($array);
            }
            else if($order === self::SORT_DESC){
                natsort($array);
                $array = array_reverse($array, true);
            }
            else{
                return false;
            }
        }
        else{
            if(!in_array($type, [self::SORT_NUMERIC, self::SORT_REGULAR, self::SORT_STRING, self::SORT_LOCALE_STRING], true)){
                return false;
            }

            if($order === self::SORT_ASC){
                asort($array, $type);
            }
            else if($order === self::SORT_DESC){
                arsort($array, $type);
            }
            else{
                return false;
            }
        }

        if(!$keepKey){
            self::tidy($array);
        }

        return true;
    }

    public static function ksort(&$array, int $order = self::SORT_ASC, int $type = self::SORT_NATURAL): bool{
        if(!self::isAvailable($array)){
            return false;
        }

        if($type == self::SORT_NATURAL){
            $keys = array_keys($array);

            if($order == self::SORT_ASC){
                natsort($keys);
            }
            else if($order == self::SORT_DESC){
                natsort($keys);
                $keys = array_reverse($keys, true);
            }
            else{
                return false;
            }

            $arr1 = [];
            foreach($keys as $key){
                foreach($array as $k => $v){
                    if($key === $k){
                        $arr1[$key] = $v;
                    }
                }
            }
            $array = $arr1;

            return true;
        }
        else{
            if(!in_array($type, [self::SORT_NUMERIC, self::SORT_REGULAR, self::SORT_STRING, self::SORT_LOCALE_STRING])){
                return false;
            }

            if($order === self::SORT_ASC){
                ksort($array, $type);
            }
            else if($order === self::SORT_DESC){
                krsort($array, $type);
            }
            else{
                return false;
            }

            return true;
        }
    }

    public static function shuffle(array $array) : array{
        shuffle($array);
        return $array;
    }

    public static function slice(array $array, int $offset, int $length = null, bool $keepKey = true): array{
        return array_slice($array, $offset, $length, $keepKey);
    }

    //merge multi array
    public static function merge(array $array): array{
        $params = func_get_args();

        if(isset($params[1])){
            self::tidy($array);
            unset($params[0]);

            foreach($params as $param){
                if(!self::isAvailable($param)){
                    continue;
                }

                foreach($param as $val){
                    if(in_array($val, $array, true)){
                        continue;
                    }

                    $array[] = $val;
                }
            }
        }

        return $array;
    }

    //merge multi array and don't check the values are unique or not
    public static function mergeAll(array $array): array{
        $params = func_get_args();

        if(isset($params[1])){
            self::tidy($array);
            unset($params[0]);

            foreach($params as $param){
                foreach($param as $val){
                    $array[] = $val;
                }
            }
        }

        return $array;
    }

    public static function mergeByKey(array $array): array{
        $params = func_get_args();
        return call_user_func_array("array_merge", $params);
    }

    public static function addToTop($value, array &$array): bool{
        return array_unshift($array, $value);
    }

    public static function addToBottom($value, array &$array): bool{
        return array_push($array, $value);
    }

    public static function popTop(array &$array){
        return array_shift($array);
    }

    public static function popBottom(array &$array){
        return array_pop($array);
    }

    public static function unique(array $array): array{
        return array_unique($array);
    }

    public static function flip(array $array) : array{
        return array_flip($array);
    }

    /**
     * @param $arr
     * @param null $key
     * @param bool $strict
     * @return bool
     */
    public static function isAvailable($arr, $key = null, bool $strict = false): bool {
        $isArray = self::isArray($arr);
        if($isArray === false){
            return false;
        }

        $isAvailable = ($isArray === true && $arr !== []) ? true : false;

        if($key === null){
            return $isAvailable;
        }

        if(self::isArray($key)){
            if(self::isAssoc($key)){
                $key = self::flip($key);
            }

            foreach($key as $k){
                $val = self::getVal($arr, $k,$dataKey, $dataType);
                if($dataKey === null){
                    return false;
                }

                if($strict && $dataType !== gettype($val)){
                    return false;
                }
            }
        }
        else{
            $val = self::getVal($arr, $key,$dataKey, $dataType);

            if($dataKey === null){
                return false;
            }

            if($strict && $dataType !== gettype($val)){
                return false;
            }
        }

        return true;
    }

    public static function key($arr){
        if(!self::isAvailable($arr)){
            return null;
        }
        $key = key($arr);
        return $key;
    }

    public static function current($arr){
        if(!self::isAvailable($arr)){
            return null;
        }
        $arr = current($arr);
        return $arr;
    }

    public static function getVal($rs, $rsKey, &$dataKey = null, &$dataType = null){
        if($rsKey === null){
            return null;
        }

        if((Str::isAvailable($rsKey) || Number::isInteric($rsKey)) && isset($rs[$rsKey])){
            $dataKey = $rsKey;
            $dataType = gettype($rs[$rsKey]);

            return $rs[$rsKey];
        }

        if(Number::isNumeric($rsKey)){
            $dataType = gettype($rsKey);
            return $rsKey;
        }

        $completeRegex = self::VAL_COMPLETE_REGEX;

        if(self::isClosure($rsKey)){
            $data = call_user_func($rsKey);
        }
        else if(preg_match($completeRegex, $rsKey, $matches)) {
            $type = $matches[1];
            $type = strtolower($type);
            $defaultValue = $matches[2];
            $key = $matches[3];
            $dataKey = $key;

            if($defaultValue == "null"){
                $defaultValue = null;
            }
            else if($defaultValue === "[]"){
                $defaultValue = [];
            }
            else if($defaultValue === "{}"){
                $defaultValue = Obj::newOne();
            }

            if (isset($rs[$key])){
                $data = $rs[$key];
            }
            else{
                $data = $defaultValue;
            }

            if($data == null && $defaultValue === null){
                $data = null;
            }
            else{
                switch ($type) {
                    case "int":
                        $data = (int)$data;
                        break;

                    case "float":
                        $data = (float)$data;
                        break;

                    case "bool":
                        $data = (bool)$data;
                        break;

                    case "string":
                        $data = (string)$data;
                        break;

                    case "array":
                        $data = (array)$data;
                        break;

                    case "object":
                        $data = (object)$data;
                        break;

                    case "void":
                    default:
                        $data = null;
                        break;
                }
            }
        }
        else{
            $data = $rsKey;
        }

        $dataType = gettype($data);

        return $data;
    }

    public static function build($mappings){
        if(!self::isAvailable($mappings)){
            return [];
        }

        $vals = [];

        foreach($mappings as $newKey => $key){
            $vals[$newKey] = self::getVal(null, $key);
        }

        return $vals;
    }

    public static function rebuild(): array{
        $params = func_get_args();
        $paramsCount = func_num_args();

        if(!$params || $paramsCount < 2){
            return [];
        }

        $arrayParams = array_slice($params, 0, ($paramsCount - 1));

        $array = [];
        if(count($arrayParams) > 1){
            foreach($arrayParams as $arrayParam){
                $array = array_merge($array, $arrayParam);
            }
        }
        else{
            $array = $arrayParams[0];
        }

        $mappings = $params[$paramsCount - 1];

        if(!self::isAvailable($mappings)){
            return [];
        }

        unset($params, $arrayParams);

        $vals = [];

        foreach($mappings as $newKey => $key){
            $vals[$newKey] = self::getVal($array, $key);
        }

        return $vals;
    }

    public static function joinToString(array $array, string $delimiter = ""){
        return implode($delimiter, $array);
    }

    public static function insert($value, int $position, array &$array): array{
        return array_splice($array, $position, 0, $value);
    }

    public static function get($key, $array){
        if(!self::isAvailable($array) || !($keys = Obj::parsePattern($key, Obj::PATTERN_ARRAY))){
            return null;
        }
        $rs = &$array;
        foreach($keys as $key){
            if(isset($rs[$key])){
                $rs = Str::isString($rs[$key]) ? trim($rs[$key]) : $rs[$key];
            }
            else{
                $rs = null;
                break;
            }
        }
        return $rs;
    }

    public static function set($key, $value, array &$array, $counter = 0):bool{
        if(!($keys = Obj::parsePattern($key, Obj::PATTERN_ARRAY)) || !Number::isUnsignedInt($counter)){
            return false;
        }
        $keysCount = count($keys);
        if($counter >= $keysCount){
            if($value === null){
                unset($array[$key]);
            }
            else if(isset($array[$key])){
                $array[$key] = $value;
            }
            return true;
        }
        foreach($keys as $key){
            if(!isset($array[$key])){
                if($value === null){
                    break;
                }
                else{
                    if(!isset($array[$key])){
                        $array[$key] = [];
                    }
                }
            }
            $counter++;
            self::set($key, $value, $array, $counter);
        }
        return true;
    }

    public static function rm($key, array &$array): bool{
        return self::set($key, null, $array);
    }

    /**
     * @param File $file
     * @return array
     * @throws InvalidDataDomainException
     */
    public static function getFromFile(File $file): array{
        if(!File::exists($file->fileLocation())){
            throw new InvalidDataDomainException("File is not exists");
        }

        $array = include($file->fileLocation());

        if(!self::isArray($array)){
            throw new InvalidDataDomainException("Not a Available array file");
        }

        return $array;
    }

    //array to object conversion
    public static function toObj($arr, bool $force = false){
        if($force){
            return (object)$arr;
        }

        if(gettype($arr) != "array"){
            return $arr;
        }

        foreach($arr as $k => $v){
            if(strripos($k, "_array") === (strlen($k) - 6) || (strripos($k, "_list") === (strlen($k) - 5))){
                $v = (array)$v;
            }
            else if(self::isArray($v) && Str::isString($k) && (strripos($k, "_object") === (strlen($k) - 7) || !preg_match("/^(?:.*(?:es|[^s]s)|(?:es|[^s]s)_[^_]*)$/i", $k))){
                $v = (object)$v;
            }

            $arr[$k] = self::toObj($v);
        }

        return $arr;
    }

    public static function toXml($array, callable $reprocessingCallback = null, bool $isBeginning = true):string{
        if(!self::isAvailable($array)){return "";}
        $xml = "";
        $reprocessingIsClosure = self::isClosure($reprocessingCallback);
        if($isBeginning && !$reprocessingIsClosure){
            $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        }
        foreach($array as $key => $val) {
            if (Number::isNumeric($val)){
                $xml .= "<{$key}>{$val}</{$key}>";
            }
            else if(self::isAvailable($val)){
                $xml .= self::toXml($val, $reprocessingCallback, false);
            }
            else{
                $xml .= "<{$key}><![CDATA[{$val}]]></{$key}>";
            }
        }
        $xml = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", " ", $xml);
        if($reprocessingIsClosure){
            $xml = call_user_func($reprocessingCallback, $xml);
        }
        return $xml;
    }

    public static function recurse($array, callable $callback) : array {
        return (self::isClosure($callback) && array_walk_recursive($array, $callback)) ? $array : [];
    }
}
