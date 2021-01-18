<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math\RPN;

use Feeler\Base\Singleton;

/**
 * Calculator class acts as singleton
 */
class Expression extends Singleton {
    private $stack;
    private $input;
    private $validator;

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
    public function execute(string $expression, bool $asBigNumber = false) {
        $input = $this->input->explode($expression);

        if (!$this->validator->validateArray($input) || !$this->validator->validateLastSign(end($input))){
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

        $firstOperand = $this->stack->pop();
        $secondOperand = $this->stack->pop();

        if($asBigNumber){
            switch ($operator) {
                case '+':
                    $result = bcadd($secondOperand, $firstOperand);
                    break;
                case '-':
                    $result = bcsub($secondOperand, $firstOperand);
                    break;
                case '*':
                    $result = bcmul($secondOperand, $firstOperand);
                    break;
                case '/':
                    if ($this->validator->operandCanDivide($firstOperand)){
                        $result = bcdiv($secondOperand, $firstOperand);
                    }
                    else{
                        $result = null;
                    }
                    break;
                default:
                    $result = null;
            }
        }
        else{
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
                    else{
                        $result = null;
                    }
                    break;
                default:
                    $result = null;
            }
        }

        if($result !== null){
            $this->stack->push($result);
        }

        return $result;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}