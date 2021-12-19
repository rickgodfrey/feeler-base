<?php

namespace Feeler\Base\Math\RPN;

class SinFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($param)
    {
        $rs = sin($param[0]->value);
        return new Operand($rs);
    }
}