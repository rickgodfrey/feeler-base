<?php

namespace Feeler\Base\Utils\RPN;

class Coma extends Token
{
    public function __construct($value)
    {
        parent::__construct("coma", $value);
    }
}