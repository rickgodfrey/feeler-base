<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math;

use Feeler\Base\Singleton;
use Feeler\Base\Utils\RPN\Stack;
use Feeler\Base\Utils\RPN\Queue;
use Feeler\Base\Utils\RPN\Tokenizer;
use Feeler\Base\Utils\RPN\RPN_Func;
use Feeler\Base\Utils\RPN\Coma;
use Feeler\Base\Utils\RPN\L_Bracket;
use Feeler\Base\Utils\RPN\R_Bracket;
use Feeler\Base\Utils\RPN\Operand;
use Feeler\Base\Utils\RPN\Operator;
use Feeler\Base\Utils\RPN\Bracket;


class Calculator extends Singleton
{
    protected $expression;
    /**
     * @var Stack
     */
    protected $stack;
    /**
     * @var Queue
     */
    protected $rpnNotation;
    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    public function calc(string $expression):string
    {
        $this->expression = preg_replace("/\\s+/i", "$1", $expression);
        $this->expression = strtr($this->expression, "{}[]", "()()");
        if (empty($this->expression)) {
            throw new \Exception("Expression to evaluate is empty");
        }
        $this->stack = new Stack();
        $this->rpnNotation = new Queue();
        $this->tokenizer = new Tokenizer($this->expression);

        $this->tokenizer->registerObject(null, "Operand", "[\\d.]+");
        $this->tokenizer->registerObject(null, "L_Bracket", "\(");
        $this->tokenizer->registerObject(null, "R_Bracket", "\)");
        $this->tokenizer->registerObject(null, "Coma", "\,");

        $this->tokenizer->registerObject("Operator", "Minus", "\-");
        $this->tokenizer->registerObject("Operator", "Plus", "\+");
        $this->tokenizer->registerObject("Operator", "Divide", "\/");
        $this->tokenizer->registerObject("Operator", "Multiply", "\*");
        $this->tokenizer->registerObject("Operator", "Power", "\^");

        $this->tokenizer->registerObject("Constant", "PI", "pi");
        $this->tokenizer->registerObject("Constant", "E", "e");

        $this->tokenizer->registerObject("Function", "Sin", "sin");
        $this->tokenizer->registerObject("Function", "Cos", "cos");
        $this->tokenizer->registerObject("Function", "Tg", "tg");
        $this->tokenizer->registerObject("Function", "Ctg", "ctg");
        $this->tokenizer->registerObject("Function", "Max", "max");

        $this->_convertToRpn();
        return $this->_evaluate();
    }

    /**
     * @link http://en.wikipedia.org/wiki/Shunting-yard_algorithm
     */
    private function _convertToRpn()
    {
        $this->tokenizer->tokenize();

        foreach ($this->tokenizer as $token) {
            if ($token instanceof Operand) {
                $this->rpnNotation->enqueue($token);
            }
            else if ($token instanceof RPN_Func) {
                $this->stack->push($token);
            }
            else if ($token instanceof Coma) {
                while (!($this->stack->top() instanceof L_Bracket)) {
                    $this->rpnNotation->enqueue($this->stack->pop());
                }
                if (!($this->stack->top() instanceof L_Bracket)) {
                    throw new \Exception("Missing left bracket in expression");
                }
            }
            else if ($token instanceof Operator) {
                if (!$this->stack->isEmpty() && ($stackTop = $this->stack->top()) && $stackTop instanceof Operator) {
                    $test1 = (in_array($token->associativity(), ["both", "left"]))
                        && ($token->priority() <= $stackTop->priority());
                    $test2 = (in_array($token->associativity(), ["right"]))
                        && ($token->priority() < $stackTop->priority());
                    if ($test1 || $test2) {
                        $this->rpnNotation->enqueue($this->stack->pop());
                    }
                }
                $this->stack->push($token);
            }
            else if ($token instanceof L_Bracket) {
                $this->stack->push($token);
            }
            else if ($token instanceof R_Bracket) {
                $leftBracketExists = false;
                while ($operator = $this->stack->pop()) {
                    if ($operator instanceof L_Bracket) {
                        $leftBracketExists = true;
                        break;
                    }
                    else {
                        $this->rpnNotation->enqueue($operator);
                    }
                }

                if ($this->stack->top() instanceof RPN_Func) {
                    $this->rpnNotation->enqueue($this->stack->pop());
                }

                if ($this->stack->isEmpty() && !$leftBracketExists) {
                    throw new \Exception("Missing left bracket in expression");
                }
            }
        }

        while (!$this->stack->isEmpty()) {
            $operator = $this->stack->pop();
            if ($operator instanceof Bracket) {
                throw new \Exception("Mismatched brackets in expression");
            }

            $this->rpnNotation->enqueue($operator);
        }
    }

    private function _evaluate():string
    {
        $tempStack = new Stack();

        while ($token = $this->rpnNotation->dequeue()) {
            if ($token instanceof Operand) {
                $tempStack->push($token);
            }
            else if (($token instanceof Operator) || ($token instanceof RPN_Func)) {
                /**
                 * @var $token Operator|RPN_Func
                 */
                if ($tempStack->count() < $token->numOfArgs()) {
                    throw new \Exception(
                        sprintf(
                            "Required %d arguments, %d given.",
                            $token->numOfArgs(),
                            $tempStack->count()
                        )
                    );
                }
                $arg = $tempStack->popMultiple($token->numOfArgs());
                $tempStack->push($token->execute(array_reverse($arg)));
            }
        }
        return (string)$tempStack->pop()->value;
    }
}