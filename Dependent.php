<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exception\InvalidDataTypeException;

class Dependent extends BaseClass {
    /**
     * Dependent constructor.
     * @throws InvalidDataTypeException
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();
        $thisReflectionObj = new \ReflectionClass(self::constructorName());
        $objs = self::getMethodAfferentObjs($thisReflectionObj);
        $thisObj = $thisReflectionObj->newInstanceArgs($objs);
        $this->overrideThisObj($thisReflectionObj, $thisObj);
    }
}