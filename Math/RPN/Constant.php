<?php

namespace Feeler\Base\Math\RPN;

abstract class Constant extends Operand
{
    public function __construct($value)
    {
        $this->type = "constant";
        $this->value = $value;

        parent::__construct($value);
    }

    public function execute()
    {
        return new Operand($this->formatDecimal($this->value));
    }
}