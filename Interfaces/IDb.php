<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Interfaces;

interface IDb{
    public function connect($dsn, $username, $password, $options = null);

    public function selectDb($dbName);

    public function query($sql);

    public function getPrimaryColumn($tableName);

    public function getConfig();

    public function setConfig();

    public function __destruct();
}