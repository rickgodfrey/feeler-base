<?php

namespace Feeler\Base\Utils\RPN;

class SinFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($arg)
    {
        return new Operand(sin($arg[0]->value));
    }
}