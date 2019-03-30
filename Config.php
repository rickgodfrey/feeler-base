<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-03-29
 * Time: 21:28
 */

namespace Feeler\Base;

class Config extends Singleton {
    use TArrayAccess;

    protected $rootPath;
    protected $configPath;
    protected $configHandler;

    protected function __construct()
    {
        parent::__construct();
    }

    protected function getDefaultSet(){
        return [

        ];
    }

    public static function &instance(){
        parent::instance();
    }

    public function loadConfig(){

    }
}