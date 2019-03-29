<?php
/**
 * Created by PhpStorm.
 * User: rickguo
 * Date: 2019-03-01
 * Time: 23:42
 */

namespace Feeler\Base;

use Feeler\Exceptions\{InvalidClassException, InvalidDataDomainException};

class Container extends BaseClass{
    const TYPE_CLASS = 1;
    const TYPE_OBJ = 2;
    const TYPE_CALLBACK = 3;

    protected $dependenciesMap = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $type
     * @return string
     */
    protected function getDependentModelName($type){
        $dict = [
            self::TYPE_CLASS => "CLASS",
            self::TYPE_OBJ => "OBJECT",
            self::TYPE_CALLBACK => "CALLBACK",
        ];

        return $dict[$type];
    }

    protected function getDependencyDictStruct($type){
        return [
            "type" => $type,
            "model_name" => $this->getDependentModelName($type),
            "sign" => null,
            "dependency" => null,
            "dependency_class" => null,
            "params" => null,
            "is_initialized" => null,
        ];
    }

    /**
     * @param $class
     * @param $dependency
     * @param $params
     * @param $type
     * @throws \Feeler\Exceptions\InvalidDataTypeException
     */
    protected function setDependenciesMap($class, $dependency, $params, $type){
        $dependencyDict = $this->getDependencyDictStruct($type);
        $params = (is_array($params)) ? $params : [];

        if($type == self::TYPE_OBJ){
            $dependencyDict["dependency"] = $dependency;
            $dependencyDict["is_initialized"] = true;
        }
        else if($type == self::TYPE_CLASS || $type == self::TYPE_CALLBACK){
            if(isset($this->dependenciesMap[$class])){
                if($this->dependenciesMap[$class]["is_initialized"] == true){
                    $sign = md5("{$params}");

                    if($this->dependenciesMap[$class]["sign"] !== $sign){
                        $dependencyDict = $this->getDependencyDictStruct($type);
                    }
                    else{ //repetitive register
                        return;
                    }
                }
                else{ //repetitive register
                    return;
                }
            }

            $dependencyDict["dependency"] = $dependency;
            $dependencyDict["params"] = $params;
            $dependencyDict["is_initialized"] = false;
        }

        Assistant::setDict($class, $dependencyDict, $this->dependenciesMap);
    }

    /**
     * @param $class
     * @param $dependency
     * @param array $params
     * @throws InvalidClassException
     * @throws \Feeler\Exceptions\InvalidDataTypeException
     * @throws InvalidDataDomainException
     */
    public function registerDependency($class, $dependency, $params = []): void{
        if(!class_exists($class)){
            throw new InvalidClassException("Non-existent Class");
        }

        if(is_string($dependency) && ($dependency = trim($dependency)) && class_exists($dependency)){
            $this->setDependenciesMap($class, $dependency, $params, self::TYPE_CLASS);
        }
        else if(is_object($dependency)){
            $this->setDependenciesMap($class, $dependency, $params, self::TYPE_OBJ);
        }
        else if(Assistant::isClousure($dependency)){
            $this->setDependenciesMap($class, $dependency, $params, self::TYPE_CALLBACK);
        }
        else{
            throw new InvalidDataDomainException("Unknown dependency type");
        }
    }

    /**
     * @param $class
     * @return object
     * @throws InvalidClassException
     * @throws InvalidDataDomainException
     * @throws \ReflectionException
     */
    public function genPreparedObj($class): object {
        if(!class_exists($class)){
            throw new InvalidClassException("Non-existent Class in the dependencies tree");
        }

        if(!isset($this->dependenciesMap[$class])){
            throw new InvalidDataDomainException("A Class has not registered a dependency in the dependencies tree");
        }

        $dependencyDict = &$this->dependenciesMap[$class];
        $type = $dependencyDict["type"];
        $dependency = &$dependencyDict["dependency"];
        $isInitialized = &$dependencyDict["is_initialized"];
        $dependencyClassName = &$dependencyDict["dependency_class"];
        $params = &$dependencyDict["params"];
        $sign = &$dependencyDict["sign"];

        if($type == self::TYPE_CLASS){
            $thisObjSign = md5("{$params}");

            if(!$isInitialized || $thisObjSign !== $sign){
                $dependencyClassName = $dependency;
                $rObj = new \ReflectionClass($dependencyClassName);
                $dependency = $rObj->newInstanceArgs($params);
                $isInitialized = true;
                $sign = $thisObjSign;
            }
        }
        else if($type == self::TYPE_CALLBACK){
            $thisObjSign = md5("{$params}");

            if(!$isInitialized || $thisObjSign !== $sign){
                $dependency = call_user_func_array($dependency, $params);
                $isInitialized = true;
                $sign = $thisObjSign;
            }
        }
        else if($type == self::TYPE_OBJ){
            $rObj = new \ReflectionClass($dependency);
            $dependencyClassName = $rObj->getName();
        }
        else{
            throw new InvalidClassException("Unknown initialization situation in the dependencies tree");
        }

        if($dependencyClassName){
            $this->genPreparedObj($dependencyClassName);
        }

        return $dependency;
    }
}