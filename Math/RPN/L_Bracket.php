<?php

namespace Feeler\Base\Math\RPN;

class L_Bracket extends Bracket
{
    public function __construct($value)
    {
        parent::__construct("l_bracket", $value);
    }
}