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

    /**
     * To Prevent The Singleton Cloning Option For Safety
     */
    protected function __clone(){
    }

    /**
     * @return static()
     * @throws \ReflectionException
     */
    public static function &instance($instanceName = null){
        // if the initialization params has been changed, the singleton instance will be regenerated
        if(Str::isAvailable($instanceName) && isset(static::$instances[$instanceName]) && is_object(static::$instances[$instanceName])){
            static::$usingInstance = static::$instances[$instanceName];
        }

        if(!is_object(static::$usingInstance)) {
            $reflectionObj = new \ReflectionClass(get_called_class());
            static::$usingInstance = $reflectionObj->newInstance();
        }

        return static::$usingInstance;
    }
}