<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

class Extension extends BaseClass{
    protected static $loadedExtensions = [];
    protected static $loadedExtensionsIsCached = false;
    protected static $checkedExtensions = [];

    public static function isAvailable(string $extension): bool {
        return Str::isAvailable($extension) and extension_loaded($extension);
    }

    public static function checkAvailability(string $extension):void{
        if(isset(self::$checkedExtensions[$extension])){
            return;
        }
        if(!self::isAvailable($extension)){
            throw new \Exception("Extension: {$extension} is not available");
        }
        self::$checkedExtensions[$extension] = true;
    }

    private static function _getAvailableExtensions(){
        self::$loadedExtensions = get_loaded_extensions(false);
        self::$loadedExtensionsIsCached = true;
        return self::$loadedExtensions;
    }

    public static function getAvailableExtensions(bool $forceRefresh = false): array{
        return self::$loadedExtensionsIsCached && !$forceRefresh ? self::$loadedExtensions : self::_getAvailableExtensions();
    }
}