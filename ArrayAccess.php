<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class ArrayAccess extends BaseClass implements \ArrayAccess {
    protected $array1cfd2761b63b0a29ed23657ea394cb2d = [];

    public function __construct()
    {
        parent::__construct();
    }

    final public function offsetExists($offset){
        return isset($this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset]) ? true : false;
    }

    final public function offsetGet($offset){
        return $this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset]($this);
    }

    final public function offsetSet($offset, $value){
        $this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset] = $value;
    }

    final public function offsetUnset($offset){
        unset($this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset]);
    }

    final public function bindArray(array &$array){
        $this->array1cfd2761b63b0a29ed23657ea394cb2d = &$array;
    }
}