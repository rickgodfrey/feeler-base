<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN\Exceptions;

class InvalidLastSignException extends BaseException{
    protected $default_message = 'RPN Calculator requires the last character to be an operator';
}