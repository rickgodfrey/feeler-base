<?php

namespace Feeler\Base\Utils\RPN;

abstract class Struct
{
    protected $buffer = [];

    public function __toString()
    {
        return implode(" ", $this->buffer);
    }

    public function size()
    {
        return count($this->buffer);
    }

    public function isEmpty()
    {
        return $this->size() == 0;
    }
}