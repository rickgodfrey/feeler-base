<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\{
    InvalidClassException,
    InvalidMethodException,
    InvalidDataTypeException,
    InvalidPropertyException
};

class BaseClass
{
    use TCommon;

    const UNKNOWN = "unknown";
    const INVOKE = "invoke";

    protected $dependencies = [];

    public function __construct(){}
    public function __destruct(){}

    protected static function constructorName(): string{
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
     * @param \ReflectionClass $reflectionObj
     * @param object $obj
     * @throws InvalidDataTypeException
     */
    final protected function selfOverride(object $obj = null, \ReflectionClass $reflectionObj = null): void{
        if(!is_object($obj)){
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

        foreach($properties as &$property){
            $propertyName = $property->name;
            if(in_array($propertyName, $staticPropertiesNames)){
                continue;
            }

            $this->setProperty($propertyName, $obj->$propertyName, true);
        }
        unset($property);
    }

    protected static function invoke(object &$reflectionObj = null, array $params = [], string &$className = null) : object {
        if(!Str::isAvailable($className)){
            $className = static::class;
        }

        if(!Str::isAvailable($className)){
            throw new InvalidClassException("Class: {$className} not exists");
        }

        (($seg2 = strrchr($className, "\\")) !== false
            and ($seg2 = substr($seg2, 1))
            and ($seg1 = substr($className, 0, (strlen($className) - strlen($seg2))))
            and ($className = $seg2) and ($layer = $seg1))
        or ($layer = "");

        $className = $layer.$className;

        if(!class_exists($className)){
            throw new InvalidClassException("Class: {$className} not exists");
        }

        if(!($reflectionObj instanceof \ReflectionClass)){
            $reflectionObj = new \ReflectionClass($className);
        }

        return $reflectionObj->newInstanceArgs($params);
    }

    protected static function invokeMethodName() : string {
        return static::INVOKE;
    }

    /**
     * @param $propertyName
     * @return mixed
     * @throws InvalidClassException
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
     * @throws InvalidClassException
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
     * @throws InvalidClassException
     * @throws InvalidMethodException
     */
    public function __call($methodName, $params)
    {

    }

    /**
     * @param $methodName
     * @param $params
     * @throws InvalidClassException
     * @throws InvalidMethodException
     */
    public static function __callStatic($methodName, $params)
    {
        throw new InvalidMethodException("Calling invalid method: " . static::classNameStatic() . "::{$methodName}()");
    }

    protected function hasProperty(string $propertyName, bool $checkVars = true): bool
    {
        return $this->enableToGetProperty($propertyName, $checkVars) || $this->enableToSetProperty($propertyName, false);
    }

    protected function hasMethod(string $methodName): bool
    {
        return method_exists($this, $methodName);
    }

    protected function enableToGetProperty(string $propertyName, bool $checkVars = true): bool
    {
        return method_exists($this, "get" . ucfirst($propertyName)) || $checkVars && property_exists($this, $propertyName);
    }

    protected function enableToSetProperty(string $propertyName, bool $checkVars = true): bool
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

        $this->$propertyName = &$value;
    }

    /**
     * @param string $propertyName
     * @param $value
     * @param bool $force
     * @throws InvalidDataTypeException
     */
    public static function setStaticProperty(string $propertyName, &$value, bool $force = false): void{
        if(is_null($propertyName)){
            throw new InvalidDataTypeException("Illegal static property setting");
        }

        if(isset(static::$$propertyName) && !is_null(static::$$propertyName) && !$force){
            return;
        }

        static::$$propertyName = &$value;
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

    public static function arrayAccessStatic($key, string $methodName){
        if($key == null || !Str::isAvailable($methodName)){
            return null;
        }

        if(!function_exists("static::{$methodName}")){
            return null;
        }

        $rs = call_user_func("static::{$methodName}");
        if(!Arr::isAvailable($rs) || !isset($rs[$key])){
            return null;
        }

        return $rs[$key];
    }

    public function arrayAccess($key, string $methodName){
        if($key == null || !Str::isAvailable($methodName)) {
            return null;
        }

        if(!method_exists($this, $methodName)){
            return null;
        }

        $rs = call_user_func([$this, $methodName]);
        if(!Arr::isAvailable($rs) || !isset($rs[$key])){
            return null;
        }

        return $rs[$key];
    }
}