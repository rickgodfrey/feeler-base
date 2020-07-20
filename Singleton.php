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
     * @throws \ReflectionException
     */
    public static function &instance(){
        if(!(static::$instance instanceof static)) {
            $reflectionObj = new \ReflectionClass(static::classNameStatic());
            $params = self::getMethodAfferentObjs($reflectionObj, self::constructorName());
            static::$instance = $reflectionObj->newInstanceArgs($params);
        }

        return static::$instance;
    }
}