<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN;

/**
 * Calculator class acts as singleton
 */
class Calculator {
    private $stack;
    private $input;
    private $validator;

    private static $instance = null;

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Constructing and injecting dependencies
     */
    private function __construct()
    {
        $this->stack = new Stack();
        $this->input = new Input();
        $this->validator = new Validator();
    }

    /**
     * Resets stack in order to run from scratch
     * @return void
     */
    public function reset()
    {
        $this->stack->clear();
    }

    /**
     * @param string $expression
     * @param bool $asBigNumber
     * @return float|\GMP|int|resource|string|null
     * @throws Exceptions\DivizionByZeroException
     * @throws Exceptions\InvalidArrayException
     * @throws Exceptions\InvalidLastSignException
     * @throws Exceptions\OperatorNotSupportedException
     */
    public function executeExpression(string $expression, bool $asBigNumber = false)
    {
        $input = $this->input->explode($expression);

        if ( !$this->validator->validateArray($input) ||
            !$this->validator->validateLastSign(end($input)) ){
            return null;
        }

        $result = "";
        foreach ($input as $value) {
            $result = $this->operate($value, $asBigNumber);

            if (is_null($result)) {
                return null;
            }
        }

        return $result;
    }

    /**
     * @param string $operator
     * @param bool $asBigNumber
     * @return float|\GMP|int|resource|string|null
     * @throws Exceptions\DivizionByZeroException
     * @throws Exceptions\OperatorNotSupportedException
     */
    public function operate(string $operator, bool $asBigNumber = false)
    {
        if ($this->stack->push($operator)) {
            return $operator;
        }

        if (!$this->validator->isValidOperator($operator)) {
            return null;
        }

        if($asBigNumber){
            $firstOperand = gmp_init((string)$this->stack->pop());
            $secondOperand = gmp_init((string)$this->stack->pop());
        }
        else{
            $firstOperand = $this->stack->pop();
            $secondOperand = $this->stack->pop();
        }

        switch ($operator) {
            case '+':
                $result = $secondOperand + $firstOperand;
                break;
            case '-':
                $result = $secondOperand - $firstOperand;
                break;
            case '*':
                $result = $secondOperand * $firstOperand;
                break;
            case '/':
                if ($this->validator->operandCanDivide($firstOperand)){
                    $result = $secondOperand / $firstOperand;
                }
                break;
            default:
                return null;
        }

        if($asBigNumber){
            $result = gmp_strval($asBigNumber);
        }

        $this->stack->push($result);

        return $result;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}