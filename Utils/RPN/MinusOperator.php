<?php

namespace Feeler\Base\Utils\RPN;

class MinusOperator extends Operator
{
    public function priority()
    {
        return 2;
    }

    public function associativity()
    {
        return "left";
    }

    public function execute($arg)
    {
        return new Operand($arg[0]->value - $arg[1]->value);
    }
}