<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exception\InvalidDataTypeException;

class ConfigProxy extends ArrayAccess implements IConfig {
    protected $rootPath;
    protected $configPath;
    protected $implement;
    protected $configData = [];

    /**
     * ConfigProxy constructor.
     * @param IConfig $implement
     */
    protected function __construct(IConfig $implement)
    {
        parent::__construct();
        $this->implement = $implement;
    }

    public function bindConfig(&$configData){
        $this->implement->bindConfig($this->configData);
    }

    /**
     * @param array $array
     * @param string|null $fromObjExp
     * @param string|null $toObjExp
     * @return bool
     * @throws InvalidDataTypeException
     */
    protected function moveData(array $array, string $fromObjExp = null, string $toObjExp = null){
        if(!$array){
            return false;
        }

        if($fromObjExp){
            $array = Arr::getByPattern($fromObjExp, $array);
        }

        if($toObjExp){
            return Arr::setByPattern($toObjExp, $array,$this->configData);
        }
        else{
            $this->configData = Arr::mergeByKey($this->configData, $array);

            return true;
        }
    }

    /**
     * @param string $file
     * @param string|null $fromObjExp
     * @param string|null $toObjExp
     * @return bool
     * @throws InvalidDataTypeException
     * @throws \Feeler\Base\Exception\InvalidDataDomainException
     */
    public function addFromFile(string $file, string $fromObjExp = null, string $toObjExp = null){
        $data = Arr::getFromFile($file);
        return $this->moveData($data, $fromObjExp, $toObjExp);
    }

    /**
     * @param array $array
     * @param string|null $fromObjExp
     * @param string|null $toObjExp
     * @return bool
     * @throws InvalidDataTypeException
     */
    public function addFromArray(array $array, string $fromObjExp = null, string $toObjExp = null){
        return $this->moveData($array, $fromObjExp, $toObjExp);
    }

    public function clearConfig(){
        $this->configData = [];
    }
}