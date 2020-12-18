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
     * @return static()
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public static function &instance(string $instanceName = "", bool $force = false) {
        $instance = TFactory::instance(TCommon::instance(), $instanceName, $force);
        if(!($instance instanceof static)){
            throw new InvalidDataDomainException("Trying to set an illegal self-instance");
        }
        return $instance;
    }
}