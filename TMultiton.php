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

    public static function &instance(string $instanceName = "", bool $force = false) {
        $instance = TFactory::instance($instanceName, $force);
        if(!($instance instanceof static)){
            throw new InvalidDataDomainException("Trying to set an illegal self-instance");
        }
        return $instance;
    }
}