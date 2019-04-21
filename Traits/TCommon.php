<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Traits;

use Feeler\Exceptions\InvalidDataTypeException;

trait TCommon{
    public static function isCallable($target){
        return is_callable($target) ? true : false;
    }

    /**
     * @param $target
     * @return mixed
     */
    public static function getCallableName($target){
        return is_callable($target, false, $callableName) ? $callableName : false;
    }

    public static function isClosure($target): bool{
        $callableName = null;

        if(is_string($target) && function_exists($target)){
            $callableName = $target;
        }

        if(!$callableName){
            $callableName = self::getCallableName($target);
        }

        if($callableName && stripos($callableName, "Closure::") === 0){
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     * @param $dict
     * @throws InvalidDataTypeException
     */
    public static function setDict(string $key, $value, array &$dict): void{
        if(!$key) {
            throw new InvalidDataTypeException("Cannot set the dict because the afferent param is wrong");
        }

        $dict[$key] = $value;
    }

    public static function isAssoc($value) {
        return is_array($value) && array_keys($value) !== range(0, count($value) - 1);
    }

    public static function isArray($value, $strict = false){
        if(!$strict){
            return is_array($value);
        }
        else{
            return is_array($value) && !self::isAssoc($value);
        }
    }
}