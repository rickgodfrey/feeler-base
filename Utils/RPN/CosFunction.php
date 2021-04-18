<?php

namespace Feeler\Base\Utils\RPN;

class CosFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($arg)
    {
        return new Operand(cos($arg[0]->value));
    }
}