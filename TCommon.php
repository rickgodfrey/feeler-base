<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
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

    protected static function getCalledClass(): string{
        return ($calledClass = get_called_class()) ? $calledClass : "";
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

        if(Str::isString($target) && function_exists($target)){
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
     * @param $reflectionObj
     * @param string $methodName
     * @return array
     * @throws \ReflectionException
     */
    protected static function getMethodAfferentObjs(\ReflectionClass $reflectionObj, string $methodName = __METHOD__): array {
        $objs = [];

        if (!$reflectionObj->hasMethod($methodName)) {
            return $objs;
        }

        $reflectionMethodObj = $reflectionObj->getMethod($methodName);
        $reflectionParams = $reflectionMethodObj->getParameters();

        $reflectionParamsCount = count($reflectionParams);
        if($reflectionParamsCount == 0) {
            return $objs;
        }

        foreach ($reflectionParams as $key => $reflectionParam) {
            $reflectionParamClassObj = $reflectionParam->getClass();

            if (!Obj::isObject($reflectionParamClassObj)) {
                throw new \ReflectionException("The Dependencies Tree of ".__CLASS__." Has Non-object Param");
            }

            $reflectionParamClassName = $reflectionParamClassObj->getName();

            $reflectionConstructionObj = new \ReflectionClass(static::constructorName());
            $reflectionParamClassParams = static::getMethodAfferentObjs($reflectionConstructionObj, $reflectionParamClassName);
            $objs[] = (new \ReflectionClass($reflectionParamClassObj->getName()))->newInstanceArgs($reflectionParamClassParams);
        }

        return $objs;
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