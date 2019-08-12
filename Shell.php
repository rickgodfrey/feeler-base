<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exception\InvalidCallException;

class Shell extends Controller {
    /**
     * Shell constructor.
     * @throws InvalidCallException
     */
    public function __construct()
    {
        parent::__construct();

        if(PHP_SAPI != "cli"){
            throw new InvalidCallException("Running Environment Error");
        }
    }


}