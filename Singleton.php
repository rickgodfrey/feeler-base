<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Singleton extends BaseClass {
    protected static $instance;

    /**
     * As a safety to prevent the singleton cloning operation
     */
    protected function __clone(){}

    /**
     * @return static()
     * @throws Exceptions\InvalidClassException
     * @throws \ReflectionException
     */
    public static function &instance(){
        $className = static::classNameStatic();
        if(!(static::$instance instanceof $className)) {
            $reflectionObj = new \ReflectionClass(static::class);
            $params = self::getMethodAfferentObjs($reflectionObj, static::constructorName());
            static::$instance = $reflectionObj->newInstanceArgs($params);
        }

        return static::$instance;
    }
}