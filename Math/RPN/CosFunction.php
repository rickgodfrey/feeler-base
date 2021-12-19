<?php

namespace Feeler\Base\Math\RPN;

class CosFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($param)
    {
        return new Operand(cos($param[0]->value));
    }
}