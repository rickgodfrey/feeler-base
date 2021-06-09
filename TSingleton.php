<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

trait TSingleton {
    use TCommon;

    protected static $instances = [];

    /**
     * As a safety to prevent the singleton cloning operation
     */
    protected function __clone(){}

    /**
     * @param array $params
     * @param bool $force
     * @return static()
     * @throws \ReflectionException
     */
    public static function instance(array $params = [], bool $force = false):object {
        $className = static::classNameStatic();
        $classSign = md5($className);
        if($force || !isset(static::$instances[$classSign]) || !(static::$instances[$classSign] instanceof $className)) {
            $reflectionObj = new \ReflectionClass($className);
            static::$instances[$classSign] = $reflectionObj->newInstanceArgs($params);
        }
        return static::$instances[$classSign];
    }
}