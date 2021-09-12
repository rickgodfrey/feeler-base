<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\MathConst;

class CtgFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($param)
    {
        return new Operand(tan(MathConst::PI / 2 - $param[0]->value));
    }
}