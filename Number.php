<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Math\MathConst;
use Feeler\Base\Math\Utils\BasicBigNumber;
use Feeler\Base\Math\Utils\BasicOperation;

class Number extends BaseClass {
    const MSG_INITIALIZATION_FAILED = "Trying to initialize an illegal number";
    const MSG_ILLEGAL_OPERATION = "Illegal number causes operation failed";
    const MSG_DIVISOR_ZERO = "Illegal divisor zero";
    const MSG_ILLEGAL_OPERATOR = "Illegal operator";
    const MSG_UNKNOWN_OPERATION = "Asking for an unknown operation";

    protected $number = "";
    protected $isFloat = false;
    protected $intPlace;
    protected $floatPlace;
    protected $scale = MathConst::DEFAULT_SCALE;
    protected $asBigNumber = false;
    protected $lastCalcStateCleared = true;
    protected $lastOperator;
    protected $lastOperandObj;

    /**
     * @return int
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * @param int $scale
     */
    public function setScale(int $scale): void
    {
        $this->scale = $scale;
    }

    /**
     * @return mixed
     */
    public function getLastOperator()
    {
        return $this->lastOperator;
    }

    /**
     * @param string $lastOperator
     * @throws \Exception
     */
    public function setLastOperator(): void
    {
        switch(__METHOD__){
            case "plus":
                $lastOperator = "+";
                break;
            case "minus":
                $lastOperator = "-";
                break;
            case "multiply":
                $lastOperator = "*";
                break;
            case "divide":
                $lastOperator = "/";
                break;
            default:
                throw new \Exception(self::MSG_ILLEGAL_OPERATOR);
        }
        $this->lastOperator = $lastOperator;
    }

    /**
     * @return mixed
     */
    public function getLastOperand()
    {
        return $this->lastOperand;
    }

    /**
     * @param Number $lastOperandObj
     */
    public function setLastOperandObj(self $lastOperandObj): void
    {
        $this->lastOperandObj = $lastOperandObj;
    }

    protected function setCalcRunningState(bool $state = true):void{
        $this->lastCalcStateCleared = $state ? false : true;
    }

    protected function pushOperation(self $numberObj):void{
        if(!($numberObj instanceof static)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        $this->executeLastOperation();
        $this->setCalcRunningState();
        $this->setLastOperator();
        $this->setLastOperandObj($numberObj);
    }

    public function __construct(string $number)
    {
        if(!Str::isAvailable($number) || !self::isNumeric($number)){
            throw new \Exception(self::MSG_INITIALIZATION_FAILED);
        }
        $this->number = $number;
        if(self::isFloaric($number)){
            $this->isFloat = true;
            $number = Str::splitToArrayByDelimiter($number, ".", 2);
            $this->intPlace = $number[0];
            $this->floatPlace = $number[1];
        }
        else{
            $this->intPlace = $number;
            $this->floatPlace = 0;
        }
    }

    protected function executeLastOperation():void{
        if($this->lastCalcStateCleared){
            return;
        }
        switch($this->getLastOperator()){
            case "+":
                $this->number = BasicOperation::plus($this->number, $this->lastOperandObj->number, $this->scale, $this->asBigNumber);
                break;
            case "-":
                $this->number = BasicOperation::minus($this->number, $this->lastOperandObj->number, $this->scale, $this->asBigNumber);
                break;
            case "*":
                $this->number = BasicOperation::multiply($this->number, $this->lastOperandObj->number, $this->scale, $this->asBigNumber);
                break;
            case "/":
                $this->number = BasicOperation::divide($this->number, $this->lastOperandObj->number, $this->scale, $this->asBigNumber);
                break;
            default:
                throw new \Exception(self::MSG_UNKNOWN_OPERATION);
        }
        $this->setCalcRunningState(false);
    }

    public function plus(object $numberObj):self{
        if(!($numberObj instanceof static)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        $this->pushOperation($numberObj);
        return $this;
    }

    public function minus(object $numberObj):self{
        if(!($numberObj instanceof static)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        $this->pushOperation($numberObj);
        return $this;
    }

    public function multiply(object $numberObj):self{
        if(!($numberObj instanceof static)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        $this->pushOperation($numberObj);
        return $this;
    }

    public function divide(object $numberObj):self{
        if(!($numberObj instanceof static)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        $this->pushOperation($numberObj);
        return $this;
    }

    public function formatDecimal(int $decimalPlaceLen = MathConst::DEFAULT_SCALE, bool $round = true, bool $fixedDecimalPlace = false, bool $showThousandsSep = false):self{
        $this->number = self::decimalFormat($this->number, $decimalPlaceLen, $round, $fixedDecimalPlace, $showThousandsSep);
        return $this;
    }

    public function rs():string{
        return $this->number;
    }

    public static function isScientificNumber($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(!preg_match("/^[0-9]+\.[0-9]+[eE]([+-])[1-9][0-9]*$/", $number)){
            return false;
        }

        return true;
    }

    public static function convertScientificToNumber($number):string{
        if(!self::isNumeric($number)){
            return "0";
        }

        if(!self::isScientificNumber($number)){
            return $number;
        }

        $number = explode("e", strtolower($number), 2);
        return isset($number[1]) ? bcmul($number[0], bcpow(10, $number[1])) : "0";
    }

    public static function autoCorrectType($number, bool $asBigNumber = false){
        if(!self::isNumeric($number)){
            throw new \Exception(self::MSG_ILLEGAL_OPERATION);
        }
        
        if(!$asBigNumber){
            if(self::isInteric($number)){
                $number = (int)$number;
            }
            else if(self::isFloaric($number)){
                $number = (float)$number;
            }
        }
        else{
            $number = (string)$number;
        }

        return $number;
    }

    public static function intMax(){
        return 9223372036854775807;
    }

    public static function isOverFlow($number):bool{
        return BasicBigNumber::isOverFlow($number);
    }

    public static function isNumber($number){
        if(!is_int($number) && !is_float($number)){
            return false;
        }

        return true;
    }

    public static function isInteric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, ".") !== false){
            return false;
        }

        return true;
    }

    public static function isFloaric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, ".") === false){
            return false;
        }

        return true;
    }

    public static function isInt($number){
        return is_int($number) ? true : false;
    }

    public static function isFloat($number){
        return is_float($number) ? true : false;
    }

    public static function isUnsignedNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number != 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedInt($number){
        if(!self::isInt($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number !== 0){
            return false;
        }

        return true;
    }

    public static function isUnsignedFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        if(strpos((string)$number, "-") !== false && $number !== 0){
            return false;
        }

        return true;
    }

    public static function isNumeric(&$number){
        $number = self::convertScientificToNumber($number);
        return is_numeric($number);
    }

    public static function isMinusNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        return self::isUnsignedNumeric($number) ? false : true;
    }

    public function isMinusInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        return self::isUnsignedInteric($number) ? false : true;
    }

    public function isMinusFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        return self::isUnsignedFloaric($number) ? false : true;
    }

    public static function isMinusInt($number){
        if(!self::isInt($number)){
            return false;
        }

        return self::isUnsignedInt($number) ? false : true;
    }

    public static function isMinusFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        return self::isUnsignedFloat($number) ? false : true;
    }

    public static function isPosiNumeric($number){
        if(!self::isNumeric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiInteric($number){
        if(!self::isInteric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiFloaric($number){
        if(!self::isFloaric($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiInt($number){
        if(!self::isInt($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function isPosiFloat($number){
        if(!self::isFloat($number)){
            return false;
        }

        return $number > 0 ? true : false;
    }

    public static function abs($number){
        if(!self::isNumeric($number)){
            return 0;
        }

        $number = abs($number);

        if(!self::isNumeric($number)){
            return 0;
        }

        return $number;
    }

    public static function decimalFormat($number, int $decimalPlaceLen = MathConst::DEFAULT_SCALE, bool $round = true, bool $fixedDecimalPlace = false, bool $showThousandsSep = false):string{
        if(!Number::isNumeric($number) || $number == 0 || !Number::isInt($decimalPlaceLen) || $decimalPlaceLen < 0){
            if($fixedDecimalPlace && Number::isPosiInteric($decimalPlaceLen)){
                return "0.".str_repeat("0", $decimalPlaceLen);
            }
            else{
                return "0";
            }
        }

        $thousandsSep = $showThousandsSep ? "," : "";
        if($round){
            $number = sprintf("%.{$decimalPlaceLen}f", number_format($number, $decimalPlaceLen, ".", $thousandsSep));
        }
        else{
            if($decimalPlaceLen == 0){
                $number = floor((float)$number);
            }
            else{
                $digit = $decimalPlaceLen + 1;
                $number = sprintf("%.{$digit}f", number_format($number, $digit, ".", $thousandsSep));
                if(Number::isFloaric($number)){
                    $numberParts = Str::splitToArrayByDelimiter((string)$number, ".", 2);
                    $decimalLen = strlen($numberParts[1]);
                    if($decimalLen > $decimalPlaceLen){
                        $numberParts[1] = substr($numberParts[1], 0, ($decimalLen - ($decimalLen - $decimalPlaceLen)));
                        $number = $numberParts[0].".".$numberParts[1];
                    }
                }
            }
        }

        if($fixedDecimalPlace && Number::isPosiInteric($decimalPlaceLen)){
            $numberParts = explode(".", (string)$number, 2);
            if(isset($numberParts[1])){
                if(($len = strlen($numberParts[1])) < $decimalPlaceLen){
                    $difference = $decimalPlaceLen - $len;
                    $number = $numberParts[0].".{$numberParts[1]}".str_repeat("0", $difference);
                }
            }
            else{
                $number = $number.".".str_repeat("0", $decimalPlaceLen);
            }
        }

        return (string)$number;
    }

    public static function decimalPlaceLength($number):int{
        if(!self::isFloaric($number)){
            return 0;
        }
        $number = (string)(float)$number;
        $number = explode(".", $number, 2);
        if(!isset($number[1])){
            return 0;
        }
        return strlen($number[1]);
    }

    public static function reserveAccuracy($number, int $decimalPlaceLength = 2):string{
        return self::decimalFormat($number, $decimalPlaceLength, false);
    }

    public static function min(){
        $params = @func_get_args();
        return call_user_func_array("min", $params);
    }

    public static function max(){
        $params = @func_get_args();
        return call_user_func_array("max", $params);
    }

    public static function randomInt($min, $max){
        return BasicOperation::randomInt($min, $max);
    }
}