<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN;

class Input{
    /**
     * Trims input and cleans spaces
     * @param string $line
     * @return string
     */
    public static function cleanLine($line) : string
    {
        return str_replace("  ", " ", trim($line));
    }

    /**
     * Explodes input to array
     * @param string $line
     * @return array
     */
    public static function explode($line) : array
    {
        return explode(' ', self::cleanLine($line));
    }
}