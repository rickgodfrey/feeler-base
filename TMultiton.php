<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

trait TMultiton  {
    use TFactory;

    /**
     * @param $instance
     * @param string $instanceName
     * @param bool $force
     * @return object
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public static function &instance(string $instanceName = "", bool $force = false) {
        static::setInstance(function($instanceName){
            $reflectionObj = new \ReflectionClass(static::classNameStatic());
            $params = static::getMethodAfferentObjs($reflectionObj, static::constructorName());
            $instance = $reflectionObj->newInstanceArgs($params);
            static::setInstance($instance, $instanceName);
        }, $instanceName, $force);
        if(!(static::usingInstance() instanceof static)){
            throw new InvalidDataDomainException("Trying to set an illegal self-instance");
        }
        /**
         * @var static()
         */
        return static::usingInstance();
    }
}