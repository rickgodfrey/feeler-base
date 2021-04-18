<?php

namespace Feeler\Base\Utils\RPN;

class PlusOperator extends Operator
{
    public function priority()
    {
        return 2;
    }

    public function associativity()
    {
        return "both";
    }

    public function execute($arg)
    {
        return new Operand($arg[0]->value + $arg[1]->value);
    }
}