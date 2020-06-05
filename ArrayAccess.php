<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class ArrayAccess implements \ArrayAccess {
    protected $array_7e1e8d83aade34fbe92c7d0ce43dcfc9 = [];

    public function __construct(){}

    public function offsetExists($offset) : bool{
        return isset($this->array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]) ? true : false;
    }

    public function offsetGet($offset){
        return $this->array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]($this);
    }

    public function offsetSet($offset, $value){
        $this->array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset] = $value;
    }

    public function offsetUnset($offset) : bool{
        unset($this->array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]);
    }
}