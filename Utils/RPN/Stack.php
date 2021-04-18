<?php

namespace Feeler\Base\Utils\RPN;

class Stack extends \SplStack
{
    public function popMultiple($cnt)
    {
        if ($cnt > $this->count()) {
            throw new \InvalidArgumentException(
                sprintf("Can't pop %d elements from datastructure with %d elements", $cnt, $this->count())
            );
        }
        $arg = [];
        while ($cnt--) {
            $arg[] = $this->pop();
        }
        return $arg;
    }
}