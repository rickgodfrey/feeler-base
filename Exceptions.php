<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use \Throwable;

class Exception extends \Exception {
    public function __construct($message = "", $code = Errno::NOERR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Exception that represents error in the program logic. This kind of
 * exceptions should directly lead to a fix in your code.
 * @link https://php.net/manual/en/class.logicexception.php
 */
class LogicException extends Exception {
}

/**
 * Exception thrown if not a valid class.
 */
class InvalidClassException extends LogicException{
}

/**
 * Exception thrown if not a valid call.
 */
class InvalidCallException extends LogicException{
}

/**
 * Exception thrown if not a valid method.
 */
class InvalidMethodException extends InvalidCallException{
}

/**
 * Exception thrown if not a valid callback.
 */
class InvalidCallbackException extends InvalidCallException{
}

/**
 * Exception thrown if not a valid function.
 */
class InvalidFuncException extends InvalidCallException{
}

/**
 * Exception thrown if a property is invalid.
 */
class InvalidPropertyException extends LogicException {
}

/**
 * Exception thrown if a value does not adhere to a defined valid data domain.
 */
class InvalidDataDomainException extends LogicException {
}

/**
 * Exception thrown if an argument does not match with the expected value.
 */
class InvalidDataTypeException extends LogicException {
}

/**
 * Exception thrown if a length is invalid.
 */
class InvalidLengthException extends LogicException {
}

/**
 * Exceptions about System.
 */
class SystemException extends LogicException {
}

/**
 * Exceptions about Network.
 */
class NetworkException extends SystemException {
}

/**
 * Exceptions about Business.
 */
class BusinessException extends LogicException {
}

/**
 * Exception thrown if an error which can only be found on runtime occurs.
 */
class RuntimeException extends Exception {
}

/**
 * Exception thrown when an illegal index was requested. This represents
 * errors that should be detected at compile time.
 */
class OutOfRangeException extends RuntimeException {
}

/**
 * Exception thrown if a value is not a valid key. This represents errors
 * that cannot be detected at compile time.
 */
class OutOfBoundsException extends RuntimeException {
}

/**
 * Exception thrown when you add an element into a full container.
 */
class OverflowException extends RuntimeException {
}

/**
 * Exception thrown to indicate range errors during program execution.
 * Normally this means there was an arithmetic error other than
 * under/overflow. This is the runtime version of
 * <b>DomainException</b>.
 */
class RangeException extends RuntimeException {
}

/**
 * Exception thrown when you try to remove an element of an empty container.
 */
class UnderflowException extends RuntimeException {
}

/**
 * Exception thrown if a value does not match with a set of values. Typically
 * this happens when a function calls another function and expects the return
 * value to be of a certain type or value not including arithmetic or buffer
 * related errors.
 */
class UnexpectedValueException extends RuntimeException {
}