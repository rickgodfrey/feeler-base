<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataTypeException;

class Dependent extends BaseClass {
    /**
     * Dependent constructor.
     * @throws InvalidDataTypeException
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();
        $reflectionObj = new \ReflectionClass(self::constructorName());
        $objs = self::getMethodAfferentObjs($reflectionObj);
        $obj = $reflectionObj->newInstanceArgs($objs);
        $this->selfOverride($obj);
    }
}