<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base\Exceptions;

use Feeler\Base\Errno;

/**
 * Exception that represents error in the program logic. This kind of
 * exceptions should directly lead to a fix in your code.
 * @link https://php.net/manual/en/class.logicexception.php
 */
class LogicException extends BaseException {
    public function __construct($message = "", $code = Errno::UNSPECIFIED, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}