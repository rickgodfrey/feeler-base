<?php

namespace Feeler\Base\Utils\RPN;

use Feeler\Base\Math\MathConst;

class CtgFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($arg)
    {
        return new Operand(tan(MathConst::PI / 2 - $arg[0]->value));
    }
}