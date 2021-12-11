<?php

namespace Feeler\Base\Math\RPN;

class MaxFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 2;
    }

    public function execute($param)
    {
        $rs = max($param[0]->value, $param[1]->value);
        return new Operand($rs);
    }
}