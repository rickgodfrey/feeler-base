<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

class Xml{
    /**
     * @param $xml
     * @return array|mixed|string
     */
    public static function toArray($xml)
    {
        if (!Str::isAvailable($xml)) {
            return [];
        }

        $xmlObj = self::isSimpleXmlLoadString($xml, LIBXML_NOCDATA);

        if (!($xmlObj instanceof \SimpleXMLElement)){
            return [];
        }

        $rs = json_decode(json_encode($xmlObj), true);
        if (!Arr::isArray($rs)) {
            return [];
        }

        return $rs;
    }

    /**
     * @param $string
     * @param string $className
     * @param int $options
     * @param string $ns
     * @param bool $is_prefix
     * @return bool|\SimpleXMLElement
     */
    public static function isSimpleXmlLoadString($string, $options = 0, $ns = "", $is_prefix = false) {
        if (preg_match("/(\<\!DOCTYPE|\<\!ENTITY)/i", $string)) {
            return false;
        }

        $className = "SimpleXMLElement";
        libxml_disable_entity_loader(true);
        return simplexml_load_string($string, $className, $options, $ns, $is_prefix);
    }
}