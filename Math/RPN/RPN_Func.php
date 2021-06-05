<?php

namespace Feeler\Base\Math\RPN;

abstract class RPN_Func extends Token
{
    public function __construct($value)
    {
        if($this->asBigNumber){
            throw new \Exception("BigNumber calculation is not supported yet");
        }
        $this->type = "function";
        $this->value = $value;

        parent::__construct($this->type, $this->value);
    }

    abstract public function execute($param);
    abstract public function numOfArgs():int;
}