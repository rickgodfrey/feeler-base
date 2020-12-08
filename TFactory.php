<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

trait TFactory {
    protected static $instances = [];
    protected static $usingInstance;
    protected static $usingInstanceName;

    protected static function classSign($instanceName){
        return md5($instanceName);
    }

    protected static function recycle($instanceName = null){
        if(!Str::isAvailable($instanceName)){
            static::$instances = [];
        }
        else{
            if(isset(static::$instances[$instanceName])){
                unset(static::$instances[$instanceName]);
            }
        }
    }

    public static function setInstance(string $instanceName, object &$instance, $force = true){
        if(!Str::isAvailable($instanceName) || !is_object($instance)){
            return false;
        }

        if(!isset(static::$instances[$instanceName]) || $force){
            static::$instances[$instanceName] = &$instance;
        }
    }

    public static function setUsingInstance(string $instanceName, object &$instance){
        if(!Str::isAvailable($instanceName) || !is_object($instance)){
            return false;
        }

        static::$usingInstance = &$instance;
        static::$usingInstanceName = $instanceName;
    }

    /**
     * @param string $instanceName
     * @param callable|null $callback
     * @param bool $force
     * @return static
     * @throws InvalidDataDomainException
     */
    public static function &instance(string $instanceName = "", callable $callback = null, bool $force = false) {
        if($instanceName === ""){
            if(Str::isAvailable(static::$usingInstanceName)){
                $instanceName = static::$usingInstanceName;
            }
            else if(defined(static::class."::DEFAULT_INSTANCE")){
                $instanceName = static::DEFAULT_INSTANCE;
            }
        }

        if(Str::isAvailable($instanceName) && isset(static::$instances[$instanceName]) && is_object(static::$instances[$instanceName])){
            $instance = static::$instances[$instanceName];
        }
        else if(!is_object(static::$usingInstance) && is_callable($callback)) {
            $instance = call_user_func($callback);
        }
        else{
            $instance = null;
        }

        if(!is_object($instance)){
            throw new InvalidDataDomainException("InvalidDataDomainException");
        }

        static::setUsingInstance($instanceName, $instance);
        static::setInstance($instanceName, $instance, $force);

        return static::$usingInstance;
    }
}