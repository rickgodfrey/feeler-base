<?php

namespace Feeler\Base\Utils\RPN;

class MaxFunction extends RPN_Func
{
    public function numOfArgs()
    {
        return 2;
    }

    public function execute($arg)
    {
        return new Operand(max($arg[0]->value, $arg[1]->value));
    }
}