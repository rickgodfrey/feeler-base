<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidClassException;
use Feeler\Base\Exceptions\InvalidDataTypeException;

trait TCommon{
    protected static $calledClassName;

    /**
     * @return string
     * @throws InvalidClassException
     */
    protected static function classNameStatic(): string
    {
        if(static::$calledClassName !== null){
            return static::$calledClassName;
        }

        static::$calledClassName = get_called_class();

        if(static::$calledClassName === false){
            throw new InvalidClassException("Cannot get the class name of the object");
        }

        return static::$calledClassName;
    }

    /**
     * @return string
     * @throws InvalidClassException
     */
    protected function className(): string {
        return static::classNameStatic();
    }

    /**
     * @return mixed
     */
    protected static function getCalledClassName()
    {
        return static::$calledClassName;
    }

    protected static function isCallable($target){
        return is_callable($target) ? true : false;
    }

    /**
     * @param $target
     * @return mixed
     */
    protected static function getCallableName($target){
        return is_callable($target, false, $callableName) ? $callableName : false;
    }

    protected static function isClosure($target): bool{
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

    protected static function isAssoc($value) {
        return is_array($value) && array_keys($value) !== range(0, count($value) - 1);
    }

    protected static function isArray($value, $strict = false){
        if(!$strict){
            return is_array($value);
        }
        else{
            return is_array($value) && !self::isAssoc($value);
        }
    }

    protected static function defined(string $constName){
        return defined(static::class."::{$constName}") ? true : false;
    }

    protected static function constName(string $constName){
        return static::defined($constName) ? static::class."::{$constName}" : null;
    }

    /**
     * @param string $constName
     * @return mixed|null
     */
    protected static function constValue(string $constName){
        return static::defined($constName) ? constant(static::class."::{$constName}") : null;
    }
}