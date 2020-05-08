<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Exceptions;

use Feeler\Base\Errno;

/**
 * Exception thrown to indicate range errors during program execution.
 * Normally this means there was an arithmetic error other than
 * under/overflow. This is the runtime version of
 * <b>DomainException</b>.
 */
class RangeException extends RuntimeException {
    public function __construct($message = "", $code = Errno::UNSPECIFIED, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}