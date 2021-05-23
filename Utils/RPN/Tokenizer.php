<?php

namespace Feeler\Base\Utils\RPN;

class Tokenizer implements \Iterator
{
    private $_expression;
    private $_registeredTokens = [];
    private $_tokenObjs = [];
    private $_iPointer = 0;
    private $_domain = "\\Feeler\\Base\\Utils\\RPN\\";

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
        $className = $this->_domain.$token["type"].$token["classSuffix"];
        $obj = new $className($value);
        return $obj;
    }

    public function tokenize()
    {
        while (strlen($this->_expression) > 0) {
            $isMatch = false;
            foreach ($this->_registeredTokens as $token) {
                $regexp = "/^({$token["regexp"]})/";
                if (!$isMatch && preg_match($regexp, $this->_expression, $matches)) {
                    $isMatch = true;
                    $this->_tokenObjs[] = $tokenObj = $this->tokenFactory($token, $matches[1]);
                    $this->_expression = substr($this->_expression, strlen($matches[1]));
                    break;
                }
            }
            if (!$isMatch) {
                throw new \Exception("Unrecognized token: '{$this->_expression}'");
            }
        }
    }

    public function registerObject($classSuffix, $type, $regexp)
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