<?php

namespace Feeler\Base\Math\RPN;

class Queue extends Struct
{
    public function enqueue($val)
    {
        $this->buffer[] = $val;
    }

    public function dequeue()
    {
        return array_shift($this->buffer);
    }
}