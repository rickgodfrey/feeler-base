<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\Utils\BasicOperation;

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

    public function execute($param)
    {
        if ($param[1]->value == 0) {
            throw new \Exception("Divide by zero");
        }
        $rs = BasicOperation::divide($param[0]->value, $param[1]->value, $this->scale, $this->asBigNumber);
        return new Operand($rs);
    }
}