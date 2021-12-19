<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\Utils\BasicOperation;

class MultiplyOperator extends Operator
{
    public function priority():int
    {
        return 3;
    }

    public function associativity()
    {
        return "both";
    }

    public function execute($param)
    {
        $rs = BasicOperation::multiply($param[0]->value, $param[1]->value, $this->scale, $this->asBigNumber);
        return new Operand($rs);
    }
}