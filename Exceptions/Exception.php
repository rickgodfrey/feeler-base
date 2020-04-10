<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Exceptions;

use Feeler\Base\Errno;
use \Throwable;

class Exception extends \Exception {
    public function __construct($message = "", $code = Errno::NOERR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}