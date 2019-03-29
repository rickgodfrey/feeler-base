<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-03-20
 * Time: 23:11
 */

namespace Feeler\Base;

class ArrayAccess extends BaseClass implements \ArrayAccess{
    protected $array1cfd2761b63b0a29ed23657ea394cb2d = [];

    public function __construct()
    {
        parent::__construct();
    }

    final public function offsetExists($offset){

    }

    final public function offsetGet($offset){
        return $this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset]($this);
    }

    final public function offsetSet($offset, $value){
        $this->array1cfd2761b63b0a29ed23657ea394cb2d[$offset] = $value;
    }

    final public function offsetUnset($offset){

    }
}