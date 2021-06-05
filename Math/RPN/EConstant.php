<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Constant\MathConst;
use Feeler\Base\Number;

class EConstant extends Constant
{
    public function __construct($value)
    {
        parent::__construct(MathConst::E);
    }
}