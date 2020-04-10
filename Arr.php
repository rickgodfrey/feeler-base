<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
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

    public function __isset($name)
    {
        return null;
    }

    public static function isArray($value, $strict = false)
    {
        return parent::isArray($value, $strict);
    }

    //Array of keys to delete and restore to incrementing key
    public static function tidy(array $array = []): array{
        if(!self::isAvailable($array)){
            return [];
        }

        $array = array_values($array);

        return $array;
    }

    public static function sort(array &$array, $order = self::SORT_ASC, $type = self::SORT_NATURAL, bool $keepKey = true): bool{
        if(!$array)
            return false;

        if($type == self::SORT_NATURAL){
            if($order == self::SORT_ASC){
                natsort($array);
            }
            else if($order == self::SORT_DESC){
                natsort($array);
                $array = array_reverse($array, true);
            }
            else
                return false;
        }
        else{
            if(!in_array($type, [self::SORT_NUMERIC, self::SORT_REGULAR, self::SORT_STRING, self::SORT_LOCALE_STRING])){
                return false;
            }

            if($order === self::SORT_ASC)
                asort($array, $type);
            else if($order === self::SORT_DESC)
                arsort($array, $type);
            else
                return false;
        }

        if(!$keepKey)
            self::tidy($array);

        return true;
    }

    public static function ksort(array &$array, $order = self::SORT_ASC, $type = self::SORT_NATURAL): bool{
        if(!is_array($array) || !$array)
            return false;

        if($type == self::SORT_NATURAL){
            $keys = array_keys($array);

            if($order == self::SORT_ASC){
                natsort($keys);
            }
            else if($order == self::SORT_DESC){
                natsort($keys);
                $keys = array_reverse($keys, true);
            }
            else
                return false;

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

            if($order === self::SORT_ASC)
                ksort($array, $type);
            else if($order === self::SORT_DESC)
                krsort($array, $type);
            else
                return false;

            return true;
        }
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
            else if(is_array($v) && is_string($k) && (strripos($k, "_object") === (strlen($k) - 7) || !preg_match("/^(?:.*(?:es|[^s]s)|(?:es|[^s]s)_[^_]*)$/i", $k))){
                $v = (object)$v;
            }

            $arr[$k] = self::toObj($v);
        }

        return $arr;
    }

    private static function _match(string $regex, string $data): array{
        preg_match($regex, $data, $matches);
        return $matches;
    }

    private static function _matchKeyWithType(string $data): array{
        return self::_match("/^\s*?\((.*?)\)\s*?(.*?)\s*$/", $data);
    }

    private static function _verify($data, string $type): bool{
        if(strtolower((string)gettype($data)) === $type)
            return true;

        return false;
    }

    public static function isAvailable($arr, $key = null, bool $strict = true): bool{
        if(!is_array($arr))
            return false;

        if(!$key)
            return $arr ? true : false;

        if(is_array($key)){
            foreach($key as $k){
                if($matches = self::_matchKeyWithType($k)){
                    $verifiedRs = self::_verify($matches[1], $matches[0]);
                    if(!$verifiedRs && $strict)
                        return false;
                    else if($verifiedRs)
                        return true;
                }
                else if(!isset($arr[$k]) && $strict)
                    return false;
                else if(isset($arr[$k]))
                    return true;
            }
        }
        else{
            if($matches = self::_matchKeyWithType($key)){
                if(!self::_verify($matches[1], $matches[0]))
                    return false;
            }
            else if(!isset($arr[$key]))
                return false;
        }

        return true;
    }

    public static function current($arr){
        if(self::isAvailable($arr))
            $arr = current($arr);
        else
            $arr = null;

        return $arr;
    }

    public static function rmVal($array, $value){
        if(self::isAvailable($array) && $value){
            foreach($array as $key => $val){
                if($val === $value){
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public static function getVal($rs, $rsKey, bool $tinyMode = false, bool $withKey = false){
        if(empty($rsKey) || (!is_string($rsKey) && !is_int($rsKey) && !is_callable($rsKey))){
            return $rsKey;
        }

        $key = $rsKey;

        if($tinyMode){
            $regex = "/^\s*(?:\(([^\(\)\:]*)(?:\:([^\(\)\:]*)?)?\))?([^\(\)]*)\s*$/i";
        }
        else{
            $regex = "/^\s*(?:\(([^\(\)\:]*)(?:\:([^\(\)\:]*)?)?\))?\{\{([^\{\}]*)\}\}\s*$/i";
        }

        if(self::isClosure($rsKey)){
            $data = call_user_func($rsKey);
        }
        else if(preg_match($regex, $rsKey, $matches)) {
            $type = $matches[1];
            $type = strtolower($type);
            $defaultValue = $matches[2];
            $key = $matches[3];

            if($defaultValue == "null"){
                $defaultValue = null;
            }
            else if($defaultValue === "[]"){
                $defaultValue = [];
            }
            else if($defaultValue === "{}"){
                $defaultValue = (new \stdClass());
            }

            if (isset($rs[$key])){
                $data = $rs[$key];
            }
            else{
                $data = $defaultValue;
            }

            if($data == "" && $defaultValue === null){
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

        return $withKey ? [$key => $data] : $data;
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

    public static function getVals($array, $keys = []){
        if(!self::isAvailable($array) || !self::isAvailable($keys)){
            return [];
        }

        $vals = [];

        foreach($keys as $key){
            $val = self::getVal($array, $key, true, true);
            $vals[key($val)] = current($val);
        }

        return $vals;
    }

    public static function isAssoc($array): bool{
        if(!self::isAvailable($array)){
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function explode(string $delimiter, $string, int $limit = -1): array{
        if(!$string){
            return [null];
        }

        if(Number::isUnsignedInt($limit) && $limit > 1){
            $array = explode($delimiter, $string, $limit);
        }
        else{
            $array = explode($delimiter, $string);
        }

        return $array;
    }

    public static function implode(string $delimiter, array $array){
        return call_user_func("implode", $delimiter, $array);
    }

    public static function get(string $key, array $array, $defaultValue = null): array{
        if(is_null($key) || $key === "") {
            return null;
        }

        if(array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach(explode('.', $key) as $segment){
            if(is_array($array) && array_key_exists($segment, $array)){
                $array = $array[$segment];
            }
            else{
                return $defaultValue;
            }
        }

        return $array;
    }

    public static function set($key, $value, array &$array): bool{
        if(!self::isAvailable($array)){
            return false;
        }

        if(!Str::isAvailable($array) && Number::isInt($key)) {
            return false;
        }

        $array[$key] = $value;

        return true;
    }

    public static function insert($value, int $position, array &$array): array{
        return array_splice($array, $position, 0, $value);
    }

    public static function rm($key, array &$array): bool{
        if(!self::isAvailable($array) || !isset($array[$key])){
            return false;
        }

        unset($array[$key]);

        return true;
    }

    /**
     * @param string $file
     * @return array
     * @throws InvalidDataDomainException
     */
    public static function getFromFile(string $file): array{
        if(!File::exists($file)){
            throw new InvalidDataDomainException("File is not exists");
        }

        $array = include($file);

        if(!self::isArray($array)){
            throw new InvalidDataDomainException("Not a Available array file");
        }

        return self::rebuild($array);
    }

    /**
     * @param string $pattern
     * @param array $array
     * @return array|mixed|null
     * @throws \Feeler\Base\Exceptions\InvalidDataTypeException
     */
    public static function getByPattern(string $pattern, array $array){
        if(!$array || !Str::isAvailable($pattern)){
            return null;
        }

        $targetArray = Obj::parsePattern($pattern, Obj::PATTERN_ARRAY);

        foreach($targetArray as $key){
            if(!isset($array[$key])){
                return null;
            }

            $array = $array[$key];
        }

        return $array;
    }

    public static function setByKeysListCallback(array $keysList, callable $callback, array &$array){
        if(!$array || !Str::isAvailable($pattern)){
            return false;
        }

        $i = 0;
        $keysListCount = count($keysList);

        foreach($array as $key => &$val){
            if(!isset($keysList[$i]) || $key != $keysList[$i]){
                return false;
            }

            Arr::popTop($keysList);
            self::setByKeysListCallback($keysList, null, $val);

            $i++;

            if($i == $keysListCount){
                $val = call_user_func($callback);
            }
        }

        return true;
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @param array $array
     * @return bool
     * @throws \Feeler\Base\Exceptions\InvalidDataTypeException
     */
    public static function setByPatternCallback(string $pattern, callable $callback, array &$array){
        if(!$array || !Str::isAvailable($pattern)){
            return false;
        }

        $keysList = (array)Obj::parsePattern($pattern, Obj::PATTERN_ARRAY);

        return self::setByKeysListCallback($keysList, $callback,$array);
    }

    /**
     * @param string $pattern
     * @param $value
     * @param array $array
     * @return bool
     * @throws \Feeler\Base\Exceptions\InvalidDataTypeException
     */
    public static function setByPattern(string $pattern, $value, array &$array){
        if(!$array || !Str::isAvailable($pattern)){
            return false;
        }

        $keysList = (array)Obj::parsePattern($pattern, Obj::PATTERN_ARRAY);

        return self::setByKeysListCallback($keysList, function() use($value){return $value;},$array);
    }

    public static function recurseCallback(){
        array_walk_recursive();
    }
}
