<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;

class PIConstant extends Constant
{
    public function __construct($value)
    {
        parent::__construct(MathConst::PI);
    }
}