<?php

namespace Feeler\Base\Utils\RPN;

class Token
{
    public $type;
    public $value;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}