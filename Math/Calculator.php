<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base\Math;

use Feeler\Base\BigNumber;
use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;
use Feeler\Base\Singleton;
use Feeler\Base\Math\RPN\Stack;
use Feeler\Base\Math\RPN\Queue;
use Feeler\Base\Math\RPN\Tokenizer;
use Feeler\Base\Math\RPN\RPN_Func;
use Feeler\Base\Math\RPN\Coma;
use Feeler\Base\Math\RPN\L_Bracket;
use Feeler\Base\Math\RPN\R_Bracket;
use Feeler\Base\Math\RPN\Operand;
use Feeler\Base\Math\RPN\Operator;
use Feeler\Base\Math\RPN\Bracket;


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
    protected $tempStack;
    protected $rs;
    protected $asBigNumber = false;
    protected $scale = MathConst::DEFAULT_SCALE;
    protected $round = true;
    protected $fixedDecimalPlace = false;
    protected $showThousandsSep = false;

    /**
     * @param bool $asBigNumber
     * @return $this
     */
    public function setAsBigNumber(bool $asBigNumber = true): self
    {
        $this->asBigNumber = $asBigNumber;
        return $this;
    }

    /**
     * @param int $scale
     * @return $this
     */
    public function setScale(int $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * @param bool $round
     * @return $this
     */
    public function setRound(bool $round = true): self
    {
        $this->round = $round;
        return $this;
    }

    /**
     * @param bool $fixedDecimalPlace
     * @return $this
     */
    public function setFixedDecimalPlace(bool $fixedDecimalPlace = true): self
    {
        $this->fixedDecimalPlace = $fixedDecimalPlace;
        return $this;
    }

    /**
     * @param bool $showThousandsSep
     * @return $this
     */
    public function setShowThousandsSep(bool $showThousandsSep = true): self
    {
        $this->showThousandsSep = $showThousandsSep;
        return $this;
    }

    protected function formatDecimal(){
        if(!Number::isNumeric($this->rs)){
            throw new \Exception("Illegal result produced");
        }
        if($this->asBigNumber){
            $this->rs = BigNumber::decimalFormat($this->rs, $this->scale, $this->round, $this->fixedDecimalPlace, $this->showThousandsSep);
        }
        else{
            $this->rs = Number::decimalFormat($this->rs, $this->scale, $this->round, $this->fixedDecimalPlace, $this->showThousandsSep);
        }
        return $this;
    }

    public function calc(string $expression):string{
        $this->expression = preg_replace("/\\s+/i", "$1", $expression);
        $this->expression = strtr($this->expression, "{}[]", "()()");
        if (empty($this->expression)) {
            throw new \Exception("Expression to evaluate is empty");
        }
        $this->stack = new Stack();
        $this->rpnNotation = new Queue();
        $this->tokenizer = new Tokenizer($this->expression);

        $this->tokenizer->setScale($this->scale);
        $this->tokenizer->setRound($this->round);
        $this->tokenizer->setFixedDecimalPlace($this->fixedDecimalPlace);
        $this->tokenizer->setShowThousandsSep($this->showThousandsSep);
        $this->tokenizer->setAsBigNumber($this->asBigNumber);

        $this->tokenizer->registerObject("", "Operand", "[\\d.]+");
        $this->tokenizer->registerObject("", "L_Bracket", "\(");
        $this->tokenizer->registerObject("", "R_Bracket", "\)");
        $this->tokenizer->registerObject("", "Coma", "\,");

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

        $this->convertToRpn()->evaluate()->rs()->formatDecimal();
        return $this->rs;
    }

    /**
     * @link http://en.wikipedia.org/wiki/Shunting-yard_algorithm
     */
    protected function convertToRpn():self
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

        return $this;
    }

    protected function evaluate():self
    {
        $this->tempStack = new Stack();

        while ($token = $this->rpnNotation->dequeue()) {
            if ($token instanceof Operand) {
                $this->tempStack->push($token);
            }
            else if (($token instanceof Operator) || ($token instanceof RPN_Func)) {
                /**
                 * @var $token Operator|RPN_Func
                 */
                if ($this->tempStack->count() < $token->numOfArgs()) {
                    throw new \Exception(
                        sprintf(
                            "Required %d arguments, %d given.",
                            $token->numOfArgs(),
                            $this->tempStack->count()
                        )
                    );
                }
                $param = $this->tempStack->popMultiple($token->numOfArgs());
                $this->tempStack->push($token->execute(array_reverse($param)));
            }
        }
        return $this;
    }

    protected function rs():self{
        $rs = ($this->tempStack instanceof Stack) ? (string)$this->tempStack->pop()->value : "";
        unset($this->tempStack);
        $this->rs = $rs;
        return $this;
    }
}