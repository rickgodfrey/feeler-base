<?php

namespace Feeler\Base\Utils\RPN;

class MaxFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 2;
    }

    public function execute($arg)
    {
        return new Operand(max($arg[0]->value, $arg[1]->value));
    }
}