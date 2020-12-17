<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

trait TMultiton  {
    use TCommon;

    protected static $instances = [];
    protected static $usingInstance;
    protected static $usingInstanceName = "";

    protected static function instanceName($instanceName):string{
        if(!Str::isAvailable($instanceName)){
            return "";
        }
        return md5(static::classNameStatic()."::{$instanceName}");
    }

    protected static function recycle($instanceName = null){
        if(!Str::isAvailable($instanceName)){
            static::$instances = [];
            static::$usingInstance = null;
            static::$usingInstanceName = "";
        }
        else{
            if(isset(static::$instances[self::instanceName($instanceName)])){
                unset(static::$instances[self::instanceName($instanceName)]);
            }
        }
    }

    public static function usingInstance():object{
        return static::$usingInstance;
    }

    public static function setInstance(string $instanceName, object &$instance, $force = true){
        if(!Str::isAvailable($instanceName) || !is_object($instance)){
            throw new InvalidDataDomainException("Trying to set an illegal instance");
        }
        if(!isset(static::$instances[self::instanceName($instanceName)]) || $force){
            static::$instances[self::instanceName($instanceName)] = &$instance;
        }
    }

    public static function setUsingInstance(string $instanceName, object &$instance){
        if(!Str::isAvailable($instanceName) || !is_object($instance) || !isset(static::$instances[self::instanceName($instanceName)])){
            throw new InvalidDataDomainException("Trying to set an illegal instance");
        }
        static::$usingInstance = &$instance;
        static::$usingInstanceName = $instanceName;
    }

    public static function defaultInstanceName():string{
        return "default_instance";
    }

    /**
     * @param string $instanceName
     * @param bool $force
     * @return object
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public static function &instance(string $instanceName = "", bool $force = false) {
        if($instanceName === ""){
            if(Str::isAvailable(static::$usingInstanceName)){
                $instanceName = static::$usingInstanceName;
            }
            else {
                $instanceName = static::defaultInstanceName();
            }
        }

        if(!Str::isAvailable($instanceName)){
            throw new InvalidDataDomainException("Trying to set an illegal instance");
        }

        if(!isset(static::$instances[self::instanceName($instanceName)]) || !is_object(static::$instances[self::instanceName($instanceName)])){
            $reflectionObj = new \ReflectionClass(static::classNameStatic());
            $params = static::getMethodAfferentObjs($reflectionObj, static::constructorName());
            static::$instances[self::instanceName($instanceName)] = $reflectionObj->newInstanceArgs($params);
        }

        static::setInstance($instanceName, static::$instances[self::instanceName($instanceName)], $force);
        static::setUsingInstance($instanceName, static::$instances[self::instanceName($instanceName)]);
        /**
         * @var static
         */
        return static::$instances[self::instanceName($instanceName)];
    }
}