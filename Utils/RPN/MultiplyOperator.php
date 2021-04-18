<?php

namespace Feeler\Base\Utils\RPN;

class MultiplyOperator extends Operator
{
    public function priority()
    {
        return 3;
    }

    public function associativity()
    {
        return "both";
    }

    public function execute($arg)
    {
        return new Operand($arg[0]->value * $arg[1]->value);
    }
}