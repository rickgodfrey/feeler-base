<?php

namespace Feeler\Base\Utils\RPN;

class Operand extends Token
{
    public function __construct($value)
    {
        $this->type = "operand";
        $this->value = $this->_normalize($value);

        parent::__construct($this->type, $this->value);
    }

    private function _normalize($value)
    {
        return floatval($value);
    }
}