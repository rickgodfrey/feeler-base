<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-03-29
 * Time: 06:27
 */

namespace Feeler\Base;

use Feeler\Exceptions\InvalidDataTypeException;

class Assistant {
    public static function getCallableName($target){
        return is_callable($target, false, $callableName) ? $callableName : false;
    }

    public static function isClousure($target){
        $callableName = null;

        if(is_string($target) && function_exists($target)){
            $callableName = $target;
        }

        if(!$callableName){
            $callableName = self::getCallableName($target);
        }

        if($callableName && strpos($callableName, "Closure::") === 0){
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
    public static function setDict($key, $value, &$dict): void{
        if(!is_array($dict) || !is_string($key) || !($key = trim($key))){
            throw new InvalidDataTypeException("Cannot set the dict because the afferent param is wrong");
        }

        $dict[$key] = $value;
    }
}