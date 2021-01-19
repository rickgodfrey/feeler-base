<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidParamException;

trait TSingleton {
    use TCommon;

    protected static $instances = [];

    /**
     * As a safety to prevent the singleton cloning operation
     */
    protected function __clone(){}

    /**
     * @return static()
     * @throws \ReflectionException
     */
    public static function instance():object {
        $className = static::classNameStatic();
        $classSign = md5($className);
        if(!isset(static::$instances[$classSign]) || !(static::$instances[$classSign] instanceof $className)) {
            $reflectionObj = new \ReflectionClass($className);
            $params = @func_get_args();
            static::$instances[$classSign] = $reflectionObj->newInstanceArgs($params);
        }

        return static::$instances[$classSign];
    }
}