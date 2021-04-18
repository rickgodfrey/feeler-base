<?php

namespace Feeler\Base\Utils\RPN;

abstract class RPN_Func extends Token
{
    public function __construct($value)
    {
        $this->type = "function";
        $this->value = $value;

        parent::__construct($this->type, $this->value);
    }

    abstract public function execute($arg);
    abstract public function numOfArgs();
}