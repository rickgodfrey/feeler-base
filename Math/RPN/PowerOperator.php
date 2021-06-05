<?php

namespace Feeler\Base\Math\RPN;

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

    public function execute($param)
    {
        $rs = pow($param[0]->value, $param[1]->value);
        return new Operand($rs);
    }
}