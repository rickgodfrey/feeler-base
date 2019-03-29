<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-03-20
 * Time: 23:28
 */

namespace Feeler\Base;

class Singleton extends BaseClass {
    private static $_instance;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * To Prevent The Singleton Cloning Option For Safety
     */
    private function __clone(){
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public static function &instance(){
        if(!is_object(static::$_instance)) {
            $reflectionObj = new \ReflectionClass(static::CLASS_NAME);
            $params = self::getMethodAfferentObjs($reflectionObj);
            static::$_instance = $reflectionObj->newInstanceArgs($params);
        }

        return static::$_instance;
    }
}