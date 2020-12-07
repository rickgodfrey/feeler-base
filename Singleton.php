<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidParamException;

class Singleton extends BaseClass {
    /**
     * As a safety to prevent the singleton cloning operation
     */
    private function __clone(){}

    /**
     * @return object
     * @throws \ReflectionException
     */
    public static function instance():object {
        return parent::instance();
    }
}