<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Number;

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
        if(!Number::isNumeric($value)){
            throw new \Exception("Illegal operand produced");
        }
        return $this->asBigNumber ? (string)$value : Number::autoCorrectType($value);
    }
}