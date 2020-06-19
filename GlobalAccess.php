<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class GlobalAccess extends BaseClass {
    private static $_varsList = ["GLOBALS", "_SERVER", "_GET", "_POST", "_FILES", "_COOKIE", "_SESSION", "_REQUEST", "_ENV"];

    private static function _access(string $varName, $key = null, $value = null){
        if(!in_array($varName, self::$_varsList) || !isset($$varName) || !Arr::isAvailable($$varName)){
            return [];
        }
        if($key === null){
            return $$varName;
        }
        if(($rs = Arr::get($key, $$varName)) !== null){
            return $rs;
        }
        return Arr::set($key, $value, $$varName);
    }

    public static function globals($key = null, $value = null){
        return self::_access("GLOBALS", $key, $value);
    }

    public static function server($key = null, $value = null){
        return self::_access("_SERVER", $key, $value);
    }

    public static function get($key = null, $value = null){
        return self::_access("_GET", $key, $value);
    }

    public static function post($key = null, $value = null){
        return self::_access("_POST", $key, $value);
    }

    public static function files($key = null, $value = null){
        return self::_access("_FILES", $key, $value);
    }

    public static function cookie($key = null, $value = null){
        return self::_access("_COOKIE", $key, $value);
    }

    public static function session($key = null, $value = null){
        return self::_access("_SESSION", $key, $value);
    }

    public static function request($key = null, $value = null){
        return self::_access("_REQUEST", $key, $value);
    }

    public static function env($key = null, $value = null){
        return self::_access("_ENV", $key, $value);
    }
}