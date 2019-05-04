<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

interface IDict{
    public function check($data);

    public function set($key, $val);

    public function get($key);

    public function has($key);

    public function getAsDefault($key);

    public function setDefault($key, $val);

    public function reset($key);
}