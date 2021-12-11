<?php

namespace Feeler\Base\Math\LinearAlgebra\Decomposition;

use Feeler\Base\Math\LinearAlgebra\NumericMatrix;

abstract class Decomposition
{
    abstract public static function decompose(NumericMatrix $M);
}
