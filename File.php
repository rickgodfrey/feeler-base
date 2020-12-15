<?php
/**
 * @link http://www.feeler.top/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license http://www.feeler.top/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;

class File extends BaseClass{
    const MODE_R = "mode_r";
    const MODE_W = "mode_w";
    const MODE_RW = "mode_rw";

    const POINTER_HEAD = "pointer_head";
    const POINTER_END = "pointer_end";

    const AM_FILE = "am_file";

    const RUNTIME_DIR = "runtime";

    protected static $rootPath;
    protected static $tempPath;
    public $segLength = 524288; //to read and write slice in segments, this set every segment's length
    public $allowUrlFile = true;

    protected $whatAmI;
    protected $state = true;
    protected $position = 0;
    protected $handle;
    protected $fileSize;
    protected $file;

    /**
     * File constructor.
     * @param string $file
     * @param string $mode
     * @param string $pointer
     * @param bool $override
     * @throws InvalidDataDomainException
     */
    public function __construct(string $file, string $mode = self::MODE_R, string $pointer = self::POINTER_HEAD, bool $override = false){
        $this->init($file, $mode, $pointer, $override);
    }

    public function __destruct(){
        if(is_resource($this->handle)){
            fclose($this->handle);
        }
    }

    /**
     * @return mixed
     */
    public static function getRootPath()
    {
        return self::$rootPath;
    }

    /**
     * @param mixed $rootPath
     */
    public static function setRootPath($rootPath): void
    {
        self::$rootPath = $rootPath;
        self::setTempPath(self::getRootPath().static::RUNTIME_DIR."/");
    }

    /**
     * @return mixed
     */
    public static function getTempPath()
    {
        return self::getTempPath();
    }

    /**
     * @param mixed $tempPath
     */
    protected static function setTempPath($tempPath): void
    {
        self::$tempPath = $tempPath;
    }

    /**
     * @param string $mode
     * @param string $pointer
     * @param bool $override
     * @return string
     * @throws InvalidDataDomainException
     */
    private function _convertModeParams(string $mode, string $pointer = self::POINTER_HEAD, bool $override = false): string{
        if(!self::defined($mode) || !self::defined($pointer)){
            throw new InvalidDataDomainException("Invalid afferent param");
        }

        $modeParam = null;

        if($mode === self::MODE_R){
            $modeParam = "r";
        }
        else if($mode == self::MODE_W){
            if($override){
                $modeParam = "w";
            }
            else{
                if($pointer == self::POINTER_HEAD){
                    $modeParam = "c";
                }
                else if($pointer == self::POINTER_END){
                    $modeParam = "a";
                }
            }
        }
        else if($mode == self::MODE_RW){
            if($override){
                $modeParam = "w+";
            }
            else{
                if($pointer == self::POINTER_HEAD){
                    $modeParam = "r+";
                }
                else if($pointer == self::POINTER_END){
                    $modeParam = "a+";
                }
            }
        }

        return $modeParam;
    }

    public function setSegLength($length): bool{
        $length = self::parseCapacityPattern($length);

        if(!$length){
            return false;
        }

        $this->segLength = $length;

        return true;
    }

    public function whatAmI(): string{
        return $this->whatAmI;
    }

    /**
     * @param $file
     * @param $mode
     * @param $pointer
     * @param $override
     * @throws InvalidDataDomainException
     */
    public function init(string $file, string $mode, string $pointer, bool $override): void{
        if(is_file($file)){
            $this->whatAmI = self::AM_FILE;
        }
        else{
            $this->state = false;
        }

        $this->file = $file;

        $lockMode = LOCK_SH;

        $modeParam = $this->_convertModeParams($mode, $pointer, $override);

        if($modeParam != "r"){
            $lockMode = LOCK_EX;
        }

        $this->handle = fopen($file, $modeParam);

        if($this->whatAmI === self::AM_FILE){
            if($this->lock($lockMode)){
                $this->fileSize = filesize($this->file);
            }
            else{
                $this->state = false;
            }
        }
        else {
            $this->fileSize = false;
        }
    }

    public function getData(int $length = -1, int $position = null){
        if($position === null){
            $position = 0;
        }

        if(!$this->state || $position < 0){
            return false;
        }

        if($length === -1){
            $length = $this->fileSize;
        }

        $originalPosition = $this->position;
        $this->seek($position);
        $data = null;

        while($dataSize = (strlen($data)) < $length){
            if(($remain = $length - $dataSize) < $this->segLength){
                $data .= fread($this->handle, $remain);
            }
            else{
                $data .= fread($this->handle, $this->segLength);
            }
        }

        $this->seek($originalPosition);

        return $data;
    }

    public function getDataCallback(callable $callback, int $position = 0, int $length = -1){
        if(!$this->state || !is_int($position) || $position < 0 || !is_callable($callback)){
            return false;
        }

        if($length === -1){
            $length = false;
        }

        $originalPosition = $this->position;
        $this->seek($position);

        if($this->fileSize === false){
            while($data = fread($this->handle, $this->segLength)){
                $callback($data);
            }
        }
        else{
            $dataSize = 0;
            while($dataSize < $length){
                if(($remain = $length - $dataSize) < $this->segLength){
                    $callback(fread($this->handle, $remain));
                    $dataSize += $this->segLength;
                }
                else{
                    $callback(fread($this->handle, $this->segLength));
                    $dataSize += $this->segLength;
                }
            }
        }

        $this->seek($originalPosition);

        return true;
    }

    public function seek($position = 0){
        if($position === null){
            $position = 0;
        }

        if(!is_int($position) || $position < 0){
            return false;
        }

        if(@fseek($this->handle, $position) !== 0){
            return false;
        }

        $this->position = $position;

        return true;
    }

    public function write($data, $length = -1){
        if(!$this->state || !$data){
            return false;
        }

        if($length === -1){
            return fwrite($this->handle, $data);
        }

        if($length > 0){
            return fwrite($this->handle, $data, $length);
        }

        return false;
    }

    public function lock($mode = LOCK_EX){
        return flock($this->handle, $mode);
    }

    public function unlock(){
        return flock($this->handle, LOCK_UN);
    }

    public static function create($file){
        return fopen($file, "w") !== false ? true : false;
    }

    /**
     * @param $file
     * @param $content
     * @param int $length
     * @return bool
     * @throws InvalidDataDomainException
     */
    public static function saveAs($file, $content, $length = -1){
        $fileObj = new self($file, self::MODE_W, self::POINTER_HEAD, true);
        $fileObj->write($content, $length);

        return is_file($file) ? true : false;
    }

    //make new dirs, will create all unexist dirs on the path
    public static function mkdir($path, $chmod = 0755){
        return is_dir($path) || mkdir($path, $chmod, true);
    }

    public static function rm($target, $recursive = false){
        if(!file_exists($target)){
            return false;
        }

        if($recursive){
            if(is_dir($target)){
                $handle = opendir($target);
                while($subTarget = readdir($handle)){
                    if($subTarget !== "." && $subTarget !== ".."){
                        $position = "{$target}/{$subTarget}";
                        if(is_dir($position)){
                            self::rm($position);
                        }
                        else{
                            @unlink($position);
                        }
                    }
                }
                closedir($handle);
                if(rmdir($target)){
                    return true;
                }
            }
        }

        if(is_file($target)){
            return @unlink($target);
        }

        return false;
    }

    public static function tempFile(string $suffix = ""):string {
        $tempFile = self::getTempPath().sha1(random_bytes(64)).(Str::isAvailable($suffix) ? $suffix : "");
        touch($tempFile);
        return $tempFile;
    }

    public static function tempFileCallback(callable $callback, string $suffix = ""){
        if(!self::isClosure($callback)){
            return false;
        }
        $tempFile = self::tempFile($suffix);
        $rs = call_user_func($callback, $tempFile);
        File::rm($tempFile);
        return $rs;
    }

    public static function rmdir($dir){
        return self::rm($dir, true);
    }

    //read the first and last 512byte data and convert to hex then check it whether have trojans signature code or not
    public static function checkHex($file){
        $handle = fopen($file, "rb");
        $fileSize = filesize($file);
        fseek($handle, 0);

        if($fileSize > 512){
            $hexCode = bin2hex(fread($handle, 512));
            fseek($handle, $fileSize - 512);
            $hexCode .= bin2hex(fread($handle, 512));
        }
        else{
            $hexCode = bin2hex(fread($handle, $fileSize));
        }
        fclose($handle);
        /**
         * match <% (  ) %>
         * 		 <? (  ) ?>
         * 		 <script  /script>
         */
        if(preg_match("/(3c25.*?28.*?29.*?253e)|(3c3f.*?28.*?29.*?3f3e)|(3C534352495054.*?2F5343524950543E)|(3C736372697074.*?2F7363726970743E)/is", $hexCode)){
            return false;
        }
        else{
            return true;
        }
    }

    public static function getPathInfo($file){
        return pathinfo($file);
    }

    //get the extension of the file
    public static function getExt($fileName){
        if(is_file($fileName)){
            $fileInfo = pathinfo($fileName);
            return isset($fileInfo["extension"]) ? $fileInfo["extension"] : null;
        }
        else{
            return strtolower(substr(strrchr($fileName, "."), 1));
        }
    }

    public static function getName($fileName){
        if((!$pathInfo = pathinfo($fileName))){
            return null;
        }
        return $pathInfo["filename"];
    }

    public static function getFullName($file){
        if((!$pathInfo = pathinfo($file))){
            return null;
        }
        return $pathInfo["basename"];
    }

    public static function getPath($file){
        if((!$pathInfo = pathinfo($file))){
            return null;
        }
        return "{$pathInfo["dirname"]}/";
    }

    //get file size
    public static function getSize($file){
        return is_file($file) ? filesize($file) : null;
    }

    public static function getTypesList(){
        return [
            "255216" => ["jpeg"],
            "13780" => ["png"],
            "7173" => ["gif"],
            "6677" => ["bmp"],
            "6063" => ["xml"],
            "60104" => ["html"],
            "208207" => ["xls", "doc"],
            "8075" => ["zip", "docx", "xlsx"],
            "8297" => ["rar"],
        ];
    }

    //get file's type according to start of 2bytes binary data
    public static function getType($file){
        if(!is_file($file)){
            return null;
        }

        $handle = fopen($file, "rb");
        if(!$handle){
            return null;
        }
        $bin = fread($handle, 2);
        fclose($handle);

        if($bin){
            $strs = unpack("C2str", $bin);

            $typeCode = $strs["str1"].$strs["str2"];
            $types = self::getTypesList();

            foreach($types as $key => $type){
                if((string)$key === $typeCode){
                    return $type;
                }
            }
        }

        return null;
    }

    //get file's type according to start of 2bytes binary data
    public static function getTypeByContent($content){
        if(!is_string($content) || !$content){
            return null;
        }

        $bin = substr($content, 0, 2);

        if($bin){
            $strs = unpack("C2str", $bin);

            $typeCode = $strs["str1"].$strs["str2"];
            $types = self::getTypesList();

            foreach($types as $key => $type){
                if((string)$key === $typeCode){
                    return $type;
                }
            }
        }

        return null;
    }

    public static function exists(string $file){
        return is_file($file);
    }

    public static function pathExists(string $file){
        return file_exists($file);
    }

    public static function dirExists(string $file){
        return is_dir($file);
    }

    public static function validateCapacityPattern($length){
        if(Number::isPosiInteric($length) || $length == 0){
            return true;
        }

        if(!Str::isAvailable($length)){
            return false;
        }

        if(!preg_match("/((?:0|(?:[1-9][0-9]*)))([k|m|g|t|kb|mb|gb|tb|byte])?/i", $length)){
            return false;
        }

        return true;
    }

    public static function parseCapacityPattern(string $length): string{
        if(!Str::isAvailable($length)){
            return false;
        }

        if(!preg_match("/((?:0|(?:[1-9][0-9]*)))([k|m|g|t|kb|mb|gb|tb|byte])?/i", $length, $matches)){
            return false;
        }

        $capacityNumber = $matches[0];
        $capacityUnit = $matches[1];

        if(!$capacityUnit || $capacityUnit == "byte"){
            return $capacityNumber;
        }

        switch($capacityUnit){
            case "k":
            case "kb":
                return (string)($capacityNumber * 1024);

            case "m":
            case "mb":
                return (string)($capacityNumber * 1048576);

            case "g":
            case "gb":
                return (string)($capacityNumber * 1073741824);

            case "t":
            case "tb":
                return (string)($capacityNumber * 1099511627776);
        }
    }

    public static function mv($oldPath, $newPath){
        if(!file_exists($oldPath)){
            return false;
        }

        return rename($oldPath, $newPath);
    }
}