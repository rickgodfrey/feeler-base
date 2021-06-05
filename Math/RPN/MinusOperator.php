<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\Utils\BasicOperation;

class MinusOperator extends Operator
{
    public function priority():int
    {
        return 2;
    }

    public function associativity()
    {
        return "left";
    }

    public function execute($param)
    {
        $rs = BasicOperation::minus($param[0]->value, $param[1]->value, $this->scale, $this->asBigNumber);
        return new Operand($rs);
    }
}