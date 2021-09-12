<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Math\MathConst;
use Feeler\Base\Math\Utils\BasicBigCalculation;
use Feeler\Base\Math\Utils\BasicOperation;

class BigNumber extends Number {
    protected $asBigNumber = true;

    public function __construct(string $number)
    {
        parent::__construct($number);
    }

    public static function decimalFormat($number, int $decimalPlaceLen = MathConst::DEFAULT_SCALE, bool $round = true, bool $fixedDecimalPlace = false, bool $showThousandsSep = false):string{
        if(!Number::isNumeric($number) || $number == 0 || $decimalPlaceLen < 0){
            if($fixedDecimalPlace && Number::isPosiInteric($decimalPlaceLen)){
                return "0.".str_repeat("0", $decimalPlaceLen);
            }
            else{
                return "0";
            }
        }

        $thousandsSep = $showThousandsSep ? "," : "";
        $numberPlaces = Str::splitToArrayByDelimiter($number, ".");
        $numberIntPlace = $numberPlaces[0];
        $numberDecimalPlace = isset($numberPlaces[1]) ? $numberPlaces[1] : "";
        $numberDecimalPlace = preg_replace("/^([0-9]+[1-9])?0+$/", "$1", $numberDecimalPlace);
        if($numberDecimalPlace){
            $numberIsFloat = true;
        }
        else{
            $numberIsFloat = false;
            $numberDecimalPlace = "";
        }

        if(!$round || !$numberIsFloat || $decimalPlaceLen <= 0){
            $number = BasicBigCalculation::decimalFormat($number, $decimalPlaceLen);
        }
        else{
            $decimalCarryDigit = Str::getChar($numberDecimalPlace, ($decimalPlaceLen + 1));
            if($decimalCarryDigit && (int)$decimalCarryDigit >= 5){
                $carryDigit = true;
            }
            else{
                $carryDigit = false;
            }
            $numberDecimalPlace = Str::slice($numberDecimalPlace, 0, $decimalPlaceLen);
            if($carryDigit){
                $numberDecimalPlace = BasicBigCalculation::plus($numberDecimalPlace, 1);
            }
        }

        if($thousandsSep){
            $numberIntPlace = Str::split($numberIntPlace, 3, $thousandsSep);
        }

        if($fixedDecimalPlace && Number::isPosiInteric($decimalPlaceLen)){
            if($numberIsFloat){
                if(($len = strlen($numberDecimalPlace)) < $decimalPlaceLen){
                    $difference = $decimalPlaceLen - $len;
                    $number = $numberIntPlace.".{$numberDecimalPlace}".str_repeat("0", $difference);
                }
            }
            else{
                $number = $numberIntPlace.".".str_repeat("0", $decimalPlaceLen);
            }
        }

        return (string)$number;
    }

    public static function randomInt($min, $max){
        return BasicOperation::randomInt($min, $max, true);
    }
}