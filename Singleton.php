<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidParamException;

class Singleton extends BaseClass {
    protected static $instances = [];

    /**
     * As a safety to prevent the singleton cloning operation
     */
    private function __clone(){}

    /**
     * @return static()
     * @throws \ReflectionException
     */
    public static function instance():object {
        $className = static::classNameStatic();
        $classSign = md5($className);
        if(!isset(static::$instances[$classSign]) || !(static::$instances[$classSign] instanceof $className)) {
            $reflectionObj = new \ReflectionClass($className);
            $params = static::getMethodAfferentObjs($reflectionObj, static::constructorName());
            static::$instances[$classSign] = $reflectionObj->newInstanceArgs($params);
        }

        return static::$instances[$classSign];
    }
}