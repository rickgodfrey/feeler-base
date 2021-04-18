<?php

namespace Feeler\Base\Utils\RPN;

use Feeler\Base\Math\MathConst;

class PIConstant extends Constant
{
    public function __construct($value)
    {
        parent::__construct(MathConst::PI);
    }
}