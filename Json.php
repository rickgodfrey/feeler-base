<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

class Json{
    public static function encode($value):string {
        return json_encode($value);
    }

    public static function decode(string $json, bool $isAssoc = false){
        return json_decode($json, $isAssoc);
    }
}