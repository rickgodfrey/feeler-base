<?php

namespace Feeler\Base\Utils\RPN;

class TgFunction extends RPN_Func
{
    public function numOfArgs():int
    {
        return 1;
    }

    public function execute($arg)
    {
        return new Operand(tan($arg[0]->value));
    }
}