<?php

namespace Feeler\Base\Utils\RPN;

abstract class Operator extends Token
{
    public function __construct($value)
    {
        $this->type = "operator";
        $this->value = $value;

        parent::__construct($this->type, $this->value);
    }

    abstract public function priority():int;

    abstract public function associativity();

    abstract public function execute($arg);

    public function numOfArgs()
    {
        return 2;
    }
}