<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

trait TFactory {
    protected static $instances;
    protected static $usingInstance;
    protected static $usingInstanceName;

    protected static function recycle($instanceName = null){
        if(!Str::isAvailable($instanceName)){
            static::$instances = null;
        }
        else{
            if(isset(static::$instances[$instanceName])){
                unset(static::$instances[$instanceName]);
            }
        }
    }

    public static function setInstance(string $instanceName, object $instance){
        if(!Str::isAvailable($instanceName) || !is_object($instance)){
            return false;
        }

        static::$instances[$instanceName] = $instance;
    }

    /**
     * @param string $instanceName
     * @param callable $callback
     * @param bool $force
     * @return static()
     */
    public static function &instance(string $instanceName = "", callable $callback = null, bool $force = false){
        // if the initialization params has been changed, the singleton instance will be regenerated
        if($instanceName === ""){
            if(Str::isAvailable(static::$usingInstanceName)){
                $instanceName = static::$usingInstanceName;
            }
            else if(defined(static::class."::DEFAULT_INSTANCE")){
                $instanceName = static::DEFAULT_INSTANCE;
            }
        }

        if(Str::isAvailable($instanceName) && isset(static::$instances[$instanceName]) && is_object(static::$instances[$instanceName])){
            static::$usingInstance = static::$instances[$instanceName];
            static::$usingInstanceName = $instanceName;
        }

        if(!is_object(static::$usingInstance) || $force) {
            if(is_callable($callback)){
                static::$usingInstance = call_user_func($callback);
                static::$usingInstanceName = $instanceName;
            }
        }

        return static::$usingInstance;
    }
}