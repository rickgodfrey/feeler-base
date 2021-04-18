<?php

namespace Feeler\Base\Utils\RPN;

class DivideOperator extends Operator
{
    public function priority():int
    {
        return 3;
    }

    public function associativity():string
    {
        return "left";
    }

    public function execute($arg)
    {
        if ($arg[1]->value == 0) {
            throw new \Exception("Divide by zero");
        }
        return new Operand($arg[0]->value / $arg[1]->value);
    }
}