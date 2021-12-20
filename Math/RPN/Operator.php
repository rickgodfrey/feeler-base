<?php

namespace Feeler\Base\Math\RPN;

abstract class Operator extends Token
{
    public function __construct($value)
    {
        parent::__construct("operator", $value);
    }

    abstract public function priority():int;

    abstract public function associativity();

    abstract public function execute($param);

    public function numOfArgs()
    {
        return 2;
    }
}