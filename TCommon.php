<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataTypeException;

trait TCommon{
    protected static function constructorName(): string{
        return "__construct";
    }

    /**
     * @return string
     */
    protected static function classNameStatic(): string
    {
        return static::class;
    }

    /**
     * @return string
     */
    protected function className(): string {
        return static::classNameStatic();
    }

    protected static function calledByClass(): string{
        return ($calledClass = get_called_class()) ? $calledClass : "";
    }

    protected static function isCallable($target):bool{
        return (Str::isAvailable($target) && is_callable($target)) ? true : false;
    }

    /**
     * @param $target
     * @return string
     */
    protected static function getCallableName($target):string{
        return (is_callable($target, false, $callableName) && Str::isAvailable($callableName)) ? $callableName : false;
    }

    protected static function isClosure($target): bool{
        if(!Obj::isObject($target) || !($target instanceof \Closure)){
            return false;
        }
        return true;
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

    /**
     * @param string $constName
     * @return bool
     */
    protected static function defined(string $constName):bool{
        return (Str::isAvailable($constName) && ($constName = strtoupper($constName)) && defined(static::class."::{$constName}")) ? true : false;
    }

    /**
     * @param string $constName
     * @return string
     */
    protected static function constName(string $constName):string{
        return static::defined($constName) ? static::class."::".strtoupper($constName) : "";
    }

    /**
     * @param string $constName
     * @return mixed|null
     */
    protected static function constValue(string $constName){
        return (Str::isAvailable($constName) && ($constName = strtoupper($constName)) && static::defined($constName)) ? constant(static::class."::{$constName}") : null;
    }

    /**
     * @param object|null $obj
     * @param \ReflectionClass|null $reflectionObj
     * @throws InvalidDataTypeException
     * @throws \ReflectionException
     */
    protected function selfOverride(object $obj = null, \ReflectionClass $reflectionObj = null): void{
        if(!Obj::isObject($obj)){
            return;
        }

        $className = get_class($obj);

        if(!($reflectionObj instanceof \ReflectionClass) || $reflectionObj->name !== $className){
            $reflectionObj = new \ReflectionClass($obj);
        }

        $properties = $reflectionObj->getProperties();
        $staticProperties = $reflectionObj->getStaticProperties();
        $staticPropertiesNames = [];

        foreach($staticProperties as $propertyName => &$value){
            static::setStaticProperty($propertyName, $className::$$propertyName, true);
            Arr::addToBottom($propertyName, $staticPropertiesNames);
        }
        unset($value);

        foreach($properties as $property){
            $propertyName = $property->name;
            if(in_array($propertyName, $staticPropertiesNames)){
                continue;
            }

            $this->setProperty($propertyName, $obj->$propertyName, true);
        }
        unset($property);
    }
}