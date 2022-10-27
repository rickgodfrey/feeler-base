<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\BigNumber;
use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;
use Feeler\Base\Singleton;

class Token extends Singleton
{
    public $type;
    public $value;

    protected $scale = MathConst::DEFAULT_SCALE;
    protected $round = true;
    protected $fixedDecimalPlace = false;
    protected $showThousandsSep = false;
    protected $asBigNumber = false;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @param int $scale
     */
    public function setScale(int $scale): void
    {
        $this->scale = $scale;
    }

    /**
     * @param bool $round
     */
    public function setRound(bool $round): void
    {
        $this->round = $round;
    }

    /**
     * @param bool $fixedDecimalPlace
     */
    public function setFixedDecimalPlace(bool $fixedDecimalPlace): void
    {
        $this->fixedDecimalPlace = $fixedDecimalPlace;
    }

    /**
     * @param bool $showThousandsSep
     */
    public function setShowThousandsSep(bool $showThousandsSep): void
    {
        $this->showThousandsSep = $showThousandsSep;
    }

    /**
     * @param bool $asBigNumber
     */
    public function setAsBigNumber(bool $asBigNumber): void
    {
        $this->asBigNumber = $asBigNumber;
    }

    public function formatDecimal(int|float|string $number){
        if(!Number::isNumeric($number)){
            throw new \Exception("Illegal result produced");
        }
        if($this->asBigNumber){
            $number = BigNumber::decimalFormat($number, $this->scale, $this->round, $this->fixedDecimalPlace, $this->showThousandsSep);
        }
        else{
            $number = Number::decimalFormat($number, $this->scale, $this->round, $this->fixedDecimalPlace, $this->showThousandsSep);
        }
        return $number;
    }
}