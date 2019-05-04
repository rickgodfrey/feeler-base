<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Exceptions\{
    InvalidClassException,
    InvalidMethodException,
    InvalidDataTypeException,
    InvalidPropertyException
};

class BaseClass
{
    use TCommon;

    const CLASS_NAME = __CLASS__;

    protected $dependencies = [];
    protected static $calledClassName;

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    public static function constructorName(): string{
        return "__construct";
    }

    /**
     * @param $reflectionObj
     * @param string $methodName
     * @return array
     * @throws \ReflectionException
     */
    final protected static function getMethodAfferentObjs(\ReflectionClass $reflectionObj, string $methodName = __METHOD__): array {
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

            if (!is_object($reflectionParamClassObj)) {
                throw new \ReflectionException("The Dependencies Tree of ".self::CLASS_NAME." Has Non-object Param");
            }

            $reflectionParamClassName = $reflectionParamClassObj->getName();

            $reflectionConstructionObj = new \ReflectionClass(self::constructorName());
            $reflectionParamClassParams = self::getMethodAfferentObjs($reflectionConstructionObj, $reflectionParamClassName);
            $objs[] = (new \ReflectionClass($reflectionParamClassObj->getName()))->newInstanceArgs($reflectionParamClassParams);
        }

        return $objs;
    }

    /**
     * @param $thisReflectionObj
     * @param $obj
     * @throws InvalidDataTypeException
     */
    final protected function overrideThisObj(\ReflectionClass $thisReflectionObj, object $obj): void{
        $properties = $thisReflectionObj->getProperties();
        foreach($properties as $property){
            $propertyName = $property->name;
            $this->setProperty($propertyName, $obj->$propertyName, true);
        }
    }

    /**
     * @return string
     * @throws InvalidClassException
     */
    public static function calledClassName(): string
    {
        if(self::$calledClassName !== null){
            return self::$calledClassName;
        }

        self::$calledClassName = get_called_class();

        if(self::$calledClassName === false){
            throw new InvalidClassException("Cannot get the class name of the object");
        }

        return self::$calledClassName;
    }

    /**
     * @return string
     */
    public static function className(): string{
        return __CLASS__;
    }

    /**
     * @param $propertyName
     * @return mixed
     * @throws InvalidMethodException
     * @throws InvalidPropertyException
     */
    public function __get($propertyName)
    {
        $getter = "get".ucfirst($propertyName);
        $thisProperty = $this->className()."::".$propertyName;

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        else if (method_exists($this, "set".ucfirst($propertyName))) {
            throw new InvalidMethodException("Getting write-only property: {$thisProperty}");
        }

        throw new InvalidPropertyException("Getting invalid property: {$thisProperty}");
    }

    public function __isset($propertyName)
    {
        $getter = "get".ucfirst($propertyName);

        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $propertyName
     * @throws InvalidMethodException
     */
    public function __unset($propertyName)
    {
        $setter = "set".ucfirst($propertyName);
        $getter = "get".ucfirst($propertyName);

        if (method_exists($this, $setter)) {
            $this->$setter(null);
        }
        else if (method_exists($this, $getter)) {
            throw new InvalidMethodException("Unsetting read-only property: " . $this->className() . "::{$propertyName}()");
        }
    }

    /**
     * @param $methodName
     * @param $params
     * @throws InvalidMethodException
     */
    public function __call($methodName, $params)
    {
        throw new InvalidMethodException("Calling invalid method: " . $this->className() . "::{$methodName}()");
    }

    /**
     * @param $methodName
     * @param $params
     * @throws InvalidClassException
     * @throws InvalidMethodException
     */
    public static function __callStatic($methodName, $params)
    {
        throw new InvalidMethodException("Calling invalid method: " . self::calledClassName() . "::{$methodName}()");
    }

    public function hasProperty(string $propertyName, bool $checkVars = true): bool
    {
        return $this->enableToGetProperty($propertyName, $checkVars) || $this->enableToSetProperty($propertyName, false);
    }

    public function hasMethod(string $methodName): bool
    {
        return method_exists($this, $methodName);
    }

    public function enableToGetProperty(string $propertyName, bool $checkVars = true): bool
    {
        return method_exists($this, "get" . ucfirst($propertyName)) || $checkVars && property_exists($this, $propertyName);
    }

    public function enableToSetProperty(string $propertyName, bool $checkVars = true): bool
    {
        return method_exists($this, "set" . ucfirst($propertyName)) || $checkVars && property_exists($this, $propertyName);
    }

    /**
     * @param $propertyName
     * @param $value
     * @param bool $force
     * @throws InvalidDataTypeException
     */
    public function setProperty(string $propertyName, &$value, bool $force = false): void{
        if(is_null($propertyName)){
            throw new InvalidDataTypeException("Illegal property setting");
        }

        if(isset($this->$propertyName) && !is_null($this->$propertyName) && !$force){
            return;
        }

        $this->$propertyName = $value;
    }

    /**
     * @param $objName
     * @param $dependency
     * @param bool $force
     * @throws InvalidDataTypeException
     */
    public function setDependency(string $objName, &$dependency, bool $force = false): void
    {
        $this->setProperty($objName, $dependency, $force);
        $this->dependencies[$objName] = $dependency;
    }
}