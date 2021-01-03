<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Math\RPN\Exceptions\DivizionByZeroException;
use Feeler\Base\Math\RPN\Exceptions\InvalidArrayException;
use Feeler\Base\Math\RPN\Exceptions\InvalidLastSignException;
use Feeler\Base\Math\RPN\Exceptions\InvalidOperatorsNumberException;
use Feeler\Base\Math\RPN\Exceptions\OperatorNotSupportedException;

class Validator{
    public static $acceptableOperators = ["+", "-", "*", "/"];

    /**
     * Check if given line is RPN expression
     * @param string $line
     * @return bool
     */
    public static function isExpression(string $line) : bool
    {
        $stack = Input::explode($line);
        if (count($stack)==1) {
            return false;
        }
        return true;
    }

    /**
     * Check if given line is Valid RPN expression
     * @param string $line
     * @return bool
     */
    public static function isValidExpression(string $line) : bool
    {
        if (!self::isExpression($line)) {
            return false;
        }

        $stack = Input::explode($line);

        $numbers = $operators = [];
        foreach ($stack as $row) {
            if (is_numeric($row)) {
                $numbers[] = $row;
            }elseif (self::isValidOperator($row)) {
                $operators[] = $row;
            } else {
                return false;
            }
        }

        $diff = count($numbers) - count($operators);
        if ($diff != 1) {
            throw new InvalidOperatorsNumberException();
        }

        return true;
    }

    /**
     * Checks if we have enough operands for calculation
     * @param array $stack
     * @return bool
     */
    public function validateArray(array $stack) : bool
    {
        if (count($stack) < 3) {
            throw new InvalidArrayException();
        }
        return true;
    }

    /**
     * Checks if given operator is valid
     * @param string $operator
     * @return bool
     */
    public static function isValidOperator(string $operator) : bool
    {
        $valid = in_array($operator, self::$acceptableOperators);
        if (!$valid) {
            throw new OperatorNotSupportedException('Operation '.$operator.' is not supported');
        }
        return $valid;
    }

    /**
     * Checks if last sign of expression is operator
     * @param string $sign
     * @return bool
     */
    public static function validateLastSign($sign) : bool
    {
        try {
            return self::isValidOperator($sign);
        } catch (OperatorNotSupportedException $exception) {
            throw new InvalidLastSignException();
        }
    }

    /**
     * Checks operand is NOT zero in case of division
     * @param string $operand
     * @return bool
     */
    public static function operandCanDivide($operand) : bool
    {
        if ($operand == 0) {
            throw new DivizionByZeroException();
        }
        return true;
    }
}