<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

class ArrayAccess extends BaseClass implements \ArrayAccess {
    private $_array_7e1e8d83aade34fbe92c7d0ce43dcfc9 = [];

    public function offsetExists($offset):bool{
        return isset($this->_array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]);
    }

    public function offsetGet($offset){
        return $this->_array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]($this);
    }

    public function offsetSet($offset, $value):void{
        $this->_array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset] = $value;
    }

    public function offsetUnset($offset):void{
        unset($this->_array_7e1e8d83aade34fbe92c7d0ce43dcfc9[$offset]);
    }
}