<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

trait TFactory  {
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

    /**
     * @return object
     */
    protected static function &usingInstance():object{
        return static::$usingInstance;
    }

    /**
     * @param $instance
     * @param string $instanceName
     * @param bool $force
     * @throws InvalidDataDomainException
     */
    public static function setInstance($instance, string $instanceName = "", $force = true):void{
        if(!Str::isAvailable($instanceName)){
            if($instanceName === ""){
                $instanceName = static::defaultInstanceName();
            }
        }

        if(!Str::isAvailable($instanceName)){
            throw new InvalidDataDomainException("Trying to get an illegal instance");
        }

        if(!Obj::isObject($instance)){
            if(!self::isClosure($instance)){
                throw new InvalidDataDomainException("Trying to set an illegal instance");
            }
            $instance = call_user_func($instance);
        }
        if(!Obj::isObject($instance)){
            throw new InvalidDataDomainException("Trying to set an illegal instance");
        }

        if(!isset(static::$instances[self::instanceName($instanceName)]) || $force){
            static::$instances[self::instanceName($instanceName)] = $instance;
        }
        static::setUsingInstance($instance, $instanceName);
    }

    protected static function setUsingInstance(object &$instance, string $instanceName){
        if(!Str::isAvailable($instanceName) || !Obj::isObject($instance) || !isset(static::$instances[self::instanceName($instanceName)])){
            throw new InvalidDataDomainException("Trying to set an illegal instance");
        }
        static::$usingInstance = &$instance;
        static::$usingInstanceName = $instanceName;
    }

    public static function defaultInstanceName():string{
        return "default_instance";
    }

    /**
     * @param $instance
     * @param string $instanceName
     * @param bool $force
     * @return object
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public static function &instance($instance, string $instanceName = "", bool $force = false) {
        static::setInstance($instance, $instanceName, $force);
        return static::usingInstance();
    }
}