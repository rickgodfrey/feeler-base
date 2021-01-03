<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN\Exceptions;

class BaseException extends \Exception{
    protected $message;
    protected $default_message = 'RPN Calculator base exception';

    /**
     * Constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $this->message = !empty($message) ? $message : $this->default_message;
        parent::__construct($message, $code, $previous);
    }
}