<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\Utils\BasicOperation;

class PlusOperator extends Operator
{
    public function priority():int
    {
        return 2;
    }

    public function associativity()
    {
        return "both";
    }

    public function execute($param)
    {
        $rs = BasicOperation::plus($param[0]->value, $param[1]->value, $this->scale, $this->asBigNumber);
        return new Operand($rs);
    }
}