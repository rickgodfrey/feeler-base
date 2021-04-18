<?php

namespace Feeler\Base\Utils\RPN;

class PowerOperator extends Operator
{
    public function priority():int
    {
        return 4;
    }

    public function associativity():string
    {
        return "right";
    }

    public function execute($arg)
    {
        return new Operand(pow($arg[0]->value, $arg[1]->value));
    }
}