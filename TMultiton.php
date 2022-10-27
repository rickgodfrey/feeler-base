<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

trait TMultiton  {
    use TFactory;

    /**
     * @param $instance
     * @param string $instanceName
     * @param bool $force
     * @return static()
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public static function &instance(string $instanceName = "", array $params = [], bool $force = false):self {
        static::setInstance(function()use($params){
            $reflectionObj = new \ReflectionClass(static::classNameStatic());
            $instance = $reflectionObj->newInstanceArgs($params);
            static::setInstance($instance);
            return static::usingInstance();
        }, $instanceName, $force);
        if(!(static::usingInstance() instanceof static)){
            throw new InvalidDataDomainException("Trying to set an illegal self-instance");
        }
        return static::usingInstance();
    }
}