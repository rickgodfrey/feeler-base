<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataTypeException;

class Obj extends BaseClass {
    const PATTERN_ARRAY = "PATTERN_ARRAY";
    const PATTERN_CALLABLE_NAME = "PATTERN_CALLABLE_NAME";

    public static function toArr($obj, $inBranch = false){
        if(!is_object($obj)){
            if($inBranch){
                return $obj;
            }
            else{
                return [];
            }
        }

        $arr = (array)$obj;
        unset($obj);

        if(is_array($arr)){
            foreach($arr as $key => $val){
                $arr[$key] = self::toArr($val, true);
            }
        }

        return $arr;
    }

    public static function newborn(){
        return new \stdClass();
    }

    public static function isAvailable($obj){
        if(!is_object($obj)){
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
        if(!is_object($obj)){
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
     * @return false|mixed|string|string[]|null
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
                    $namespace = Arr::implode("\\", $namespace);
                    $className = $namespace."\\".$matches[$matchesCount - 1];
                    $methodName = $matches[$matchesCount];
                }

                $rs = $className ? "{$className}::{$methodName}" : $methodName;

                if(!self::isCallable($rs)){
                    return null;
                }
                break;

            case self::PATTERN_ARRAY:
                $rs = explode(".", $exp);
                break;

            default:
                $rs = null;
                break;
        }

        return $rs;
    }
}