<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataTypeException;

class Obj extends BaseClass {
    const PATTERN_ARRAY = "pattern_array";
    const PATTERN_CALLABLE_NAME = "pattern_callable_name";

    public static function toArr($obj, $inBranch = false){
        if(!self::isObject($obj)){
            if($inBranch){
                return $obj;
            }
            else{
                return [];
            }
        }

        $arr = (array)$obj;
        unset($obj);

        if(Arr::isArray($arr)){
            foreach($arr as $key => $val){
                $arr[$key] = self::toArr($val, true);
            }
        }

        return $arr;
    }

    public static function newOne(){
        return new \stdClass();
    }

    public static function isObject($value) :bool {
        return is_object($value);
    }

    public static function isAvailable($obj){
        if(!self::isObject($obj)){
            return false;
        }

        $obj = self::toArr($obj);

        return empty($obj) ? false : true;
    }

    /**
     * @param $obj
     * @param $methodName
     * @param null $rObj
     * @return bool
     * @throws InvalidDataTypeException
     * @throws \ReflectionException
     */
    public static function hasItsOwnMethod($obj, $methodName, &$rObj = null) : bool{
        if(!self::isObject($obj)){
            throw new InvalidDataTypeException("Param 1 is not a object");
        }

        if(!($rObj instanceof \ReflectionClass)){
            $rObj = new \ReflectionClass($obj);
        }

        if(!($rObj instanceof \ReflectionClass)){
            throw new InvalidDataTypeException("Invalid reflection object");
        }

        if(!Str::isAvailable($methodName)){
            return false;
        }

        if(!method_exists($obj ,$methodName)){
            return false;
        }

        try{
            $methodReflectionObj = $rObj->getMethod($methodName);
        }
        catch(\ReflectionException $e){
            return false;
        }

        return ($methodReflectionObj->class === $rObj->getName()) ? true : false;
    }

    /**
     * @param $exp
     * @param string $mode
     * @return array|mixed|string|null
     */
    public static function parsePattern($exp, $mode = self::PATTERN_CALLABLE_NAME){
        if(!Str::isAvailable($exp) || !preg_match("/^([a-zA-Z_][a-zA-Z_0-9]+)(\.[a-zA-Z_][a-zA-Z_0-9]+)*$/", $exp, $matches)){
            return null;
        }

        $className = null;
        $methodName = null;

        Arr::popTop($matches);
        $matchesCount = count($matches);

        switch($mode){
            case self::PATTERN_CALLABLE_NAME:
                if($matchesCount == 1){
                    $methodName = $matches[0];
                }
                else if($matchesCount == 2){
                    $className = "\\{$matches[0]}";
                    $methodName = $matches[1];
                }
                else{
                    $namespace = Arr::slice($matches, 0, $matchesCount - 2);
                    $namespace = Arr::join("\\", $namespace);
                    $className = $namespace."\\".$matches[$matchesCount - 1];
                    $methodName = $matches[$matchesCount];
                }

                $rs = $className ? "{$className}::{$methodName}" : $methodName;

                if(!self::isCallable($rs)){
                    return null;
                }
                break;

            case self::PATTERN_ARRAY:
                $rs = Str::split(".", $exp);
                if(!$rs){
                    return null;
                }
                break;

            default:
                $rs = null;
                break;
        }

        return $rs;
    }
}