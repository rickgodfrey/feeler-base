<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

class Table extends Arr{
    const SORT_ASC = parent::SORT_ASC;
    const SORT_DESC = parent::SORT_DESC;
    const SORT_NATURAL = parent::SORT_NATURAL;
    const SORT_NUMERIC = parent::SORT_NUMERIC;
    const SORT_STRING = parent::SORT_STRING;
    const SORT_REGULAR = parent::SORT_REGULAR;
    const SORT_LOCALE_STRING = parent::SORT_LOCALE_STRING;

    //Sort the 2D array by the 2nd dimension value, according is the 2nd dimension key
    public static function sortByField(&$array, $field, int $order = self::SORT_ASC, int $type = self::SORT_NATURAL, $keepKey = false) : bool{
        if(!Arr::isAvailable($array) || !Str::isAvailable($field)){
            return false;
        }

        $arr1 = $arr2 = [];

        foreach($array as $key => $val){
            if(isset($val[$field])) {
                $arr1[$key] = $val[$field];
            }
        }

        if($type === self::SORT_NATURAL){
            if($order === self::SORT_ASC){
                natsort($arr1);
            }
            else if($order === self::SORT_DESC){
                natsort($arr1);
                $arr1 = array_reverse($arr1, true);
            }
            else
                return false;
        }
        else{
            if(!in_array($type, [self::SORT_NUMERIC, self::SORT_REGULAR, self::SORT_STRING, self::SORT_LOCALE_STRING])){
                return false;
            }

            if($order === self::SORT_ASC)
                asort($arr1, $type);
            else if($order === self::SORT_DESC)
                arsort($arr1, $type);
            else
                return false;
        }

        if($keepKey){
            foreach($arr1 as $key => $val){
                $arr2[$key] = $array[$key];
            }
        }
        else{
            foreach($arr1 as $key => $val){
                $arr2[] = $array[$key];
            }
        }

        $array = $arr2;
        return true;
    }

    public static function indexByKey($array, $indexKey, $columns = null, $uniqueItem = false) : array {
        if(!Arr::isAvailable($array) || !Str::isAvailable($indexKey)){
            return [];
        }

        $rs = [];

        foreach($array as $key => $value){
            if(!isset($value[$indexKey]) || $value[$indexKey] == null){
                continue;
            }

            if(!$uniqueItem && !isset($rs[$value[$indexKey]])){
                $rs[$value[$indexKey]] = [];
            }

            if($columns === null){
                if(!$uniqueItem){
                    $rs[$value[$indexKey]][] = $value;
                }
                else{
                    $rs[$value[$indexKey]] = $value;
                }
            }
            else if(Str::isAvailable($columns)){
                if(!$uniqueItem){
                    $rs[$value[$indexKey]][] = isset($value[$columns]) ? $value[$columns] : null;
                }
                else{
                    $rs[$value[$indexKey]] = isset($value[$columns]) ? $value[$columns] : null;
                }
            }
            else if(Arr::isAvailable($columns)){
                if(!$uniqueItem){
                    $row = [];

                    foreach($columns as $column){
                        if($column == null){
                            continue;
                        }

                        $row[$column] = isset($value[$column]) ? $value[$column] : null;
                    }

                    $rs[$value[$indexKey]][] = $row;
                }
                else{
                    if(!isset($rs[$value[$indexKey]])){
                        $rs[$value[$indexKey]] = [];
                    }

                    foreach($columns as $column){
                        if($column == null){
                            continue;
                        }

                        $rs[$value[$indexKey]][$column] = isset($value[$column]) ? $value[$column] : null;
                    }
                }
            }
        }

        return $rs;
    }

    public static function getColumn(array $rs, $field, $assocKey = null): array{
        return array_column($rs, $field, $assocKey);
    }

    //merge multi array and make the values different
    public static function merge(array $array): array{
        $params = func_get_args();
        $array = [];

        if(isset($params[1])){
            foreach($params as $param){
                if(Arr::isArray($param)){
                    foreach($param as $val){
                        if(!in_array($val, $array, true)){
                            $array[] = $val;
                        }
                    }
                }
            }
        }
        self::tidy($array);

        return $array;
    }
}