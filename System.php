<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class System extends Singleton implements IConfig {
    const EOL = PHP_EOL;

    protected $configData;

    public function bindConfig(&$configData): void{
        $this->configData = &$configData;
    }
}