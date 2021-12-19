<?php

namespace Feeler\Base\Math\RPN;

use Feeler\Base\BigNumber;
use Feeler\Base\Math\MathConst;
use Feeler\Base\Number;

class Token
{
    public $type;
    public $value;

    protected $scale = MathConst::DEFAULT_SCALE;
    protected $round = true;
    protected $fixedDecimalPlace = false;
    protected $showThousandsSep = false;
    protected $asBigNumber = false;

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

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}