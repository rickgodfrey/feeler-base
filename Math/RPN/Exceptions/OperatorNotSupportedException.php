<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN\Exceptions;

class OperatorNotSupportedException extends BaseException{
    protected $default_message = 'Operation is not supported';
}