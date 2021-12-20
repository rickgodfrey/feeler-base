<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Number;

class Operand extends Token
{
    public function __construct($value)
    {
        parent::__construct("operand", $value);
    }

    public function normalize()
    {
        if(!Number::isNumeric($this->value)){
            throw new \Exception("Illegal operand produced");
        }

        $this->value = Number::autoCorrectType($this->value, $this->asBigNumber);
    }
}