<?php

namespace Feeler\Base\Math\RPN;

class Tokenizer implements \Iterator
{
    const RPN_DOMAIN = "\\Feeler\\Base\\Utils\\RPN\\";

    private $_expression;
    private $_registeredTokens = [];
    private $_tokenObjs = [];
    private $_iPointer = 0;
    private $_scale = 2;
    private $_round = true;
    private $_fixedDecimalPlace = false;
    private $_showThousandsSep = false;
    private $_asBigNumber = false;

    /**
     * @param int $scale
     */
    public function setScale(int $scale): void
    {
        $this->_scale = $scale;
    }

    /**
     * @param bool $round
     */
    public function setRound(bool $round): void
    {
        $this->_round = $round;
    }

    /**
     * @param bool $fixedDecimalPlace
     */
    public function setFixedDecimalPlace(bool $fixedDecimalPlace): void
    {
        $this->_fixedDecimalPlace = $fixedDecimalPlace;
    }

    /**
     * @param bool $showThousandsSep
     */
    public function setShowThousandsSep(bool $showThousandsSep): void
    {
        $this->_showThousandsSep = $showThousandsSep;
    }

    /**
     * @param bool $asBigNumber
     */
    public function setAsBigNumber(bool $asBigNumber): void
    {
        $this->_asBigNumber = $asBigNumber;
    }

    public function __construct($expr)
    {
        $this->_expression = $expr;
        $this->_expression = preg_replace("/\\s+/i", "$1", $this->_expression);
        $this->_expression = strtr($this->_expression, "{}[]", "()()");
        if (empty($this->_expression)) {
            throw new \Exception("Expression to tokenize is empty");
        }
    }

    private function tokenFactory($token, $value)
    {
        if (!isset($this->_registeredTokens[$token["type"]])) {
            throw new \Exception("Undefined token type '{$token["type"]}'");
        }
        $className = self::RPN_DOMAIN.$token["type"].$token["classSuffix"];
        $obj = new $className($value);
        call_user_func([$obj, "setScale"], $this->_scale);
        call_user_func([$obj, "setRound"], $this->_round);
        call_user_func([$obj, "setFixedDecimalPlace"], $this->_fixedDecimalPlace);
        call_user_func([$obj, "setShowThousandsSep"], $this->_showThousandsSep);
        call_user_func([$obj, "setAsBigNumber"], $this->_asBigNumber);
        return $obj;
    }

    public function tokenize()
    {
        while (strlen($this->_expression) > 0) {
            $isMatched = false;
            foreach ($this->_registeredTokens as $token) {
                $regexp = "/^({$token["regexp"]})/";
                if (!$isMatched && preg_match($regexp, $this->_expression, $matches)) {
                    $isMatched = true;
                    $this->_tokenObjs[] = $tokenObj = $this->tokenFactory($token, $matches[1]);
                    $this->_expression = substr($this->_expression, strlen($matches[1]));
                    break;
                }
            }
            if (!$isMatched) {
                throw new \Exception("Unrecognized token: '{$this->_expression}'");
            }
        }
    }

    public function registerObject(string $classSuffix, string $type, string $regexp)
    {
        $this->_registeredTokens[$type] = [
            "regexp" => $regexp,
            "type" => $type,
            "classSuffix" => $classSuffix,
        ];
    }

    public function current()
    {
        return $this->_tokenObjs[$this->_iPointer];
    }

    public function key()
    {
        return $this->_tokenObjs[$this->_iPointer]->type;
    }

    public function next()
    {
        $this->_iPointer++;
    }

    public function rewind()
    {
        $this->_iPointer = 0;
    }

    public function valid()
    {
        return ($this->_iPointer < sizeof($this->_tokenObjs));
    }
}