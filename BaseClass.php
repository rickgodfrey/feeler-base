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
    InvalidParamException,
    InvalidPropertyException
};

class BaseClass
{
    use TCommon;

    const UNKNOWN = "unknown";
    const INVOKE_METHOD = "invoke";

    protected $dependencies = [];

    protected static function invoke(object &$reflectionObj = null, array $params = [], string &$className = null) : object {
        if(!Str::isAvailable($className)){
            $className = static::classNameStatic();
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
        return static::INVOKE_METHOD;
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
     */
    public function __call($methodName, $params)
    {

    }

    /**
     * @param $methodName
     * @param $params
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
    public function setProperty(string $propertyName, $value, bool $force = false): void{
        if(is_null($propertyName)){
            throw new InvalidDataTypeException("Illegal property setting");
        }

        if(isset($this->$propertyName) && $this->$propertyName !== null && !$force){
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
    public static function setStaticProperty(string $propertyName, $value, bool $force = false): void{
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
    public function setDependency(string $objName, $dependency, bool $force = false): void
    {
        $this->setProperty($objName, $dependency, $force);
        $this->dependencies[$objName] = &$dependency;
    }

    public static function arrayAccessStatic($key, string $methodName){
        if($key == null || !Str::isAvailable($methodName)){
            return null;
        }
        if(!method_exists(static::classNameStatic(), $methodName)){
            return null;
        }
        $rs = call_user_func([static::classNameStatic(), $methodName]);
        if(!Arr::isAvailable($rs)){
            return null;
        }
        return Arr::get($key, $rs);
    }

    public function arrayAccess($key, string $methodName){
        if($key == null || !Str::isAvailable($methodName)) {
            return null;
        }
        if(!method_exists($this, $methodName)){
            return null;
        }
        $rs = call_user_func([$this, $methodName]);
        if(!Arr::isAvailable($rs)){
            return null;
        }
        return Arr::get($key, $rs);
    }
}