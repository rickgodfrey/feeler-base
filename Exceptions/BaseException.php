<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base\Exceptions;

use Feeler\Base\Errno;
use Feeler\Base\Number;
use Feeler\Base\Str;

class BaseException extends \Exception {
    public function __construct($message = "", $code = Errno::UNSPECIFIED, \Throwable $previous = null)
    {
        if(!Number::isInteric($code) || !Str::isAvailable($message)){
            $code = Errno::UNKNOWN;
            $message = "UNKNOWN_ERROR";
        }
        else{
            $code = (int)$code;
        }

        parent::__construct($message, $code, $previous);
    }
}