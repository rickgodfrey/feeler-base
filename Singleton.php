<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Singleton extends BaseClass {
    protected static $instance;

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * To Prevent The Singleton Cloning Option For Safety
     */
    protected function __clone(){
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public static function &instance(){
        // if the initialization params has been changed, the singleton instance will be regenerated
        if(!is_object(static::$instance) || func_num_args() > 0) {
            $reflectionObj = new \ReflectionClass(static::CLASS_NAME);
            $params = self::getMethodAfferentObjs($reflectionObj);
            static::$instance = $reflectionObj->newInstanceArgs($params);
        }

        return static::$instance;
    }
}