<?php

namespace Feeler\Base\Math\RPN;

class Stack extends \SplStack
{
    public function popMultiple($count)
    {
        if ($count > $this->count()) {
            throw new \InvalidArgumentException(
                sprintf("Can't pop %d elements from datastructure with %d elements", $count, $this->count())
            );
        }
        $param = [];
        while ($count--) {
            $param[] = $this->pop();
        }
        return $param;
    }
}