<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-02-15
 * Time: 00:58
 */

namespace Feeler\Base;

class Errno{
    const NOERR = 0;
    const RUNTIME_ERR = 1001;
    const LOGIC_ERR = 2001;
    const SYSTEM_ERR = 10001;
    const BUSINESS_ERR = 20001;
}