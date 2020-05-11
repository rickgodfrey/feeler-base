<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Exceptions;

use Feeler\Base\Errno;
use Feeler\Base\Number;
use Feeler\Base\Str;

class BaseException extends \Exception {
    public function __construct($message = "", $code = Errno::UNSPECIFIED, \Throwable $previous = null)
    {
        if(Number::isInteric($code)){
            $code = (int)$code;
        }
        else{
            $code = Errno::UNKNOWN;
        }

        if(!Str::isAvailable($message)){
            $message = "UNKNOWN_ERROR";
        }

        parent::__construct($message, $code, $previous);
    }
}