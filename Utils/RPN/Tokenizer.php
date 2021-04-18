<?php

namespace Feeler\Base\Utils\RPN;

class Tokenizer implements \Iterator
{
    private $expression;
    private $registeredTokens = [];
    private $tokenObjs = [];
    private $iPointer = 0;

    public function __construct($expr)
    {
        $this->expression = $expr;
        $this->expression = preg_replace("/\\s+/i", "$1", $this->expression);
        $this->expression = strtr($this->expression, "{}[]", "()()");
        if (empty($this->expression)) {
            throw new \Exception("Expression to tokenize is empty");
        }
    }

    private function tokenFactory($token, $value)
    {
        if (!isset($this->registeredTokens[$token["type"]])) {
            throw new \Exception("Undefined token type '{$token["type"]}'");
        }
        $className = $token["type"].$token["classSuffix"];
        $obj = new $className($value);
        return $obj;
    }

    public function tokenize()
    {
        while (strlen($this->expression) > 0) {
            $isMatch = false;
            foreach ($this->registeredTokens as $token) {
                $regexp = "/^({$token["regexp"]})/";

                if (!$isMatch && preg_match($regexp, $this->expression, $matches)) {
                    $isMatch = true;
                    $this->tokenObjs[] = $tokenObj = $this->tokenFactory($token, $matches[1]);
                    $this->expression = substr($this->expression, strlen($matches[1]));
                    break;
                }
            }
            if (!$isMatch) {
                throw new \Exception("Unrecognized token: '{$this->expression}'");
            }
        }
    }

    public function registerObject($classSuffix, $type, $regexp)
    {
        $this->registeredTokens[$type] = [
            "regexp" => $regexp,
            "type" => $type,
            "classSuffix" => $classSuffix,
        ];
    }

    public function current()
    {
        return $this->tokenObjs[$this->iPointer];
    }

    public function key()
    {
        return $this->tokenObjs[$this->iPointer]->type;
    }

    public function next()
    {
        $this->iPointer++;
    }

    public function rewind()
    {
        $this->iPointer = 0;
    }

    public function valid()
    {
        return ($this->iPointer < sizeof($this->tokenObjs));
    }
}