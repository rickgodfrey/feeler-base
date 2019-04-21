<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Extension extends BaseClass{
    public static function isAvailable(string $extension): bool {
        return extension_loaded($extension);
    }

    public static function getAvailableExtensions(bool $showZendExtensions = false): array{
        return get_loaded_extensions($showZendExtensions);
    }
}