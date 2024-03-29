<?php
/**
 * @link https://www.feeler.cc/
 * @copyright Copyright (c) 2019 Rick Guo
 * @license https://www.feeler.cc/license/
 */

namespace Feeler\Base;

use Feeler\Base\Exceptions\InvalidDataDomainException;
use Feeler\Base\Exceptions\InvalidValueException;
use Feeler\Base\Exceptions\UnexpectedValueException;
use Feeler\Base\Math\Utils\BasicOperation;

class File extends Multiton{
    const MODE_R = "mode_r";
    const MODE_W = "mode_w";
    const MODE_RW = "mode_rw";

    const POINTER_HEAD = "pointer_head";
    const POINTER_END = "pointer_end";

    const AM_FILE = "am_file";
    const AM_DIR = "am_dir";
    const AM_VOID = "am_void";
    const RUNTIME_DIR = "runtime";

    const CAPACITY_UNIT_BYTE = "";
    const CAPACITY_UNIT_KB = "kb";
    const CAPACITY_UNIT_MB = "mb";
    const CAPACITY_UNIT_GB = "gb";
    const CAPACITY_UNIT_TB = "tb";
    const CAPACITY_UNIT_LB = "lb";

    protected static string $rootPath;
    protected static string $tempPath;
    protected string|int $segLength = 524288; //To read and write file-data in segments, this sets every segment's length
    protected string $whatAmI;
    protected bool $state = false;
    protected int $position = 0;
    protected mixed $handle;

    protected string $fileName;
    protected string $fileExt;
    protected string $fileLocation;
    protected string $fileDir;
    protected string $fileSrc;
    protected int $fileSize;
    protected string $fileMd5Sign;

    /**
     * File constructor.
     * @param string $fileLocation
     * @param string $mode
     * @param string $pointer
     * @param bool $override
     * @throws InvalidDataDomainException
     * @throws InvalidValueException
     */
    public function __construct(string $fileLocation, string $mode = self::MODE_R, string $pointer = self::POINTER_HEAD, bool $override = false){
        $this->init($fileLocation, $mode, $pointer, $override);
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
     * @return mixed
     */
    public function fileName()
    {
        return $this->fileName ? $this->fileName : ($this->fileName = self::getFullName($this->fileLocation));
    }

    /**
     * @return mixed
     */
    public function fileExt()
    {
        return $this->fileExt ? $this->fileExt : ($this->fileExt = self::getExt($this->fileLocation));
    }

    /**
     * @return mixed
     */
    public function fileLocation()
    {
        return $this->fileLocation;
    }

    /**
     * @return mixed
     */
    public function fileDir()
    {
        return $this->fileDir ? $this->fileDir : ($this->fileDir = self::getDir($this->fileLocation));
    }

    /**
     * @return mixed
     */
    public function fileSize()
    {
        return $this->fileSize ? $this->fileSize : ($this->fileSize = self::getFileSize($this->fileLocation));
    }

    /**
     * @return mixed
     */
    public function fileSrc()
    {
        return $this->fileSrc;
    }

    /**
     * @param mixed $fileSrc
     */
    public function setFileSrc($fileSrc): void
    {
        $this->fileSrc = $fileSrc;
    }

    /**
     * @return mixed
     */
    public function fileMd5Sign()
    {
        return $this->fileMd5Sign ? $this->fileMd5Sign : ($this->fileMd5Sign = @md5_file($this->fileLocation));
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

    public function setSegLength(int $length): bool{
        $length = self::parseCapacityFormat($length);
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
     * @param string $fileLocation
     * @param string $mode
     * @param string $pointer
     * @param bool $override
     * @throws InvalidDataDomainException
     * @throws InvalidValueException
     */
    public function init(string $fileLocation, string $mode, string $pointer, bool $override): void{
        if(is_file($fileLocation)){
            $this->whatAmI = self::AM_FILE;
        }
        else if(is_dir($fileLocation)){
            $this->whatAmI = self::AM_DIR;
        }
        else{
            $this->whatAmI = self::AM_VOID;
        }

        $lockMode = LOCK_SH;
        $modeParam = $this->_convertModeParams($mode, $pointer, $override);
        if($modeParam != "r"){
            $lockMode = LOCK_EX;
        }

        $this->fileLocation = self::getFileLocation($fileLocation);
        if($this->whatAmI === self::AM_FILE){
            $this->handle = fopen($fileLocation, $modeParam);
            if($this->lock($lockMode)){
                $this->state = true;
                $this->fileSize = filesize($this->file);
            }
        }
        else {
            $this->fileSize = false;
        }
        $this->fileName = self::getFullName($fileLocation);
        $this->fileExt = self::getExt($fileLocation);
        $this->fileDir = self::getDir($fileLocation);
    }

    public function getContent(int $length = -1, int $position = null):string{
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
        $data = false;

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

    public function getContentCallback(callable $callback, int $position = 0, int $length = -1):bool{
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

    public function seek($position = 0):bool{
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

    public function write($data, $length = -1):int{
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

    public function mvTo(string $newPath){
        return self::mv($this->fileLocation, $newPath);
    }

    public static function mv(string $oldPath, string $newPath):bool{
        return Str::isAvailable($oldPath) && Str::isAvailable($newPath) && file_exists($oldPath) ? rename($oldPath, $newPath) : false;
    }

    public static function create($fileLocation):bool{
        return fopen($fileLocation, "w") !== false ? true : false;
    }

    /**
     * @param $fileLocation
     * @param $content
     * @param int $length
     * @return bool
     * @throws InvalidDataDomainException
     * @throws InvalidValueException
     */
    public static function saveAs($fileLocation, $content, $length = -1):bool{
        $fileObj = new static($fileLocation, self::MODE_W, self::POINTER_HEAD, true);
        $fileObj->write($content, $length);

        return is_file($fileLocation);
    }

    //make new dirs, will create all not exist dirs on the path
    public static function mkdir($path, $chmod = 0755):bool{
        return is_dir($path) || mkdir($path, $chmod, true);
    }

    public static function rm($target, $recursive = false):bool{
        if(!file_exists($target)){
            return false;
        }
        if($recursive && is_dir($target)){
            $handle = opendir($target);
            while($subTarget = readdir($handle)){
                if($subTarget !== "." && $subTarget !== ".."){
                    $position = "{$target}/{$subTarget}";
                    is_dir($position) ? self::rm($position) : @unlink($position);
                }
            }
            closedir($handle);
            if(rmdir($target)){
                return true;
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

    public static function rmdir($dir):bool{
        return self::rm($dir, true);
    }

    //read the first and last 512byte data and convert to hex then check it whether have trojans signature code or not
    public static function checkHex($fileLocation):bool{
        $handle = fopen($fileLocation, "rb");
        $fileSize = filesize($fileLocation);
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

    public static function getPathInfo($fileLocation, string $key = null){
        if(!Arr::isArray(($pathInfo = pathinfo($fileLocation)))){
            return false;
        }
        if($key === null){
            return $pathInfo;
        }
        return (Str::isAvailable($key) && isset($pathInfo[$key])) ? $pathInfo[$key] : false;
    }

    //get the extension of the file
    public static function getExt($fileLocation):string{
        if(is_file($fileLocation)){
            return self::getPathInfo($fileLocation, "extension");
        }
        else{
            return strtolower(substr(strrchr($fileLocation, "."), 1));
        }
    }

    public static function getName($fileLocation):string{
        return self::getPathInfo($fileLocation, "filename");
    }

    public static function getFullName($fileLocation):string{
        return self::getPathInfo($fileLocation, "basename");
    }

    public static function getPath($fileLocation):string{
        if(!($path = self::getPathInfo($fileLocation, "dirname"))){
            return false;
        }
        return "{$path}/";
    }

    public static function getDir($fileLocation):string{
        if(!($path = self::getPath($fileLocation))){
            return false;
        }
        $path = Str::splitToArrayByDelimiter($path, "/");
        return (($count = count($path)) >= 2 && isset($path[($count - 2)])) ? $path[($count - 2)] : false;
    }

    public static function getFileLocation($fileLocation):string{
        return self::getPath($fileLocation).self::getFullName($fileLocation);
    }

    //get file size
    public static function getFileSize($fileLocation):int{
        return is_file($fileLocation) ? filesize($fileLocation) : false;
    }

    public static function getTypesList():array{
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
    public static function getType($fileLocation){
        if(!is_file($fileLocation)){
            return false;
        }

        $handle = fopen($fileLocation, "rb");
        if(!$handle){
            return false;
        }
        $bin = fread($handle, 2);
        fclose($handle);

        if(!$bin){
            return false;
        }

        $strs = unpack("C2str", $bin);

        $typeCode = $strs["str1"].$strs["str2"];
        $types = self::getTypesList();

        foreach($types as $key => $type){
            if((string)$key === $typeCode){
                return $type;
            }
        }

        return false;
    }

    //get file's type according to start of 2bytes binary data
    public static function getTypeByContent($content){
        if(!Str::isString($content) || !$content){
            return false;
        }

        $bin = substr($content, 0, 2);

        if(!$bin){
            return false;
        }

        $strs = unpack("C2str", $bin);

        $typeCode = $strs["str1"].$strs["str2"];
        $types = self::getTypesList();

        foreach($types as $key => $type){
            if((string)$key === $typeCode){
                return $type;
            }
        }

        return false;
    }

    public static function exists(string $fileLocation):bool{
        return is_file($fileLocation);
    }

    public static function pathExists(string $fileLocation):bool{
        return file_exists($fileLocation);
    }

    public static function dirExists(string $fileLocation):bool{
        return is_dir($fileLocation);
    }

    public static function parseCapacityFormat(string $length, string $convertToUnit = self::CAPACITY_UNIT_BYTE): string{
        if(!Str::isAvailable($length)){
            return false;
        }

        if(!preg_match("/(0|[1-9][0-9]*)(k|m|g|t|p|kb|mb|gb|tb|pb)?/i", $length, $matches)){
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
                $number2 = 1024;
                break;
            case "m":
            case "mb":
                $number2 = 1048576;
                break;
            case "g":
            case "gb":
                $number2 = 1073741824;
                break;
            case "t":
            case "tb":
                $number2 = 1099511627776;
                break;
            case "p":
            case "pb":
                $number2 = 1125899906842624;
                break;
        }
        $capacityNumber = BasicOperation::multiply($capacityNumber, $number2, 0, true);

        switch($convertToUnit){
            case self::CAPACITY_UNIT_BYTE:
                break;
            case self::CAPACITY_UNIT_KB:
                $number2 = 1024;
                break;
            case self::CAPACITY_UNIT_MB:
                $number2 = 1048576;
                break;
            case self::CAPACITY_UNIT_GB:
                $number2 = 1073741824;
                break;
            case self::CAPACITY_UNIT_TB:
                $number2 = 1099511627776;
                break;
            case self::CAPACITY_UNIT_LB:
                $capacityNumber = 1125899906842624;
                break;
            default:
                throw new UnexpectedValueException();
        }
        $capacityNumber = BasicOperation::divide($capacityNumber, $number2, 2, true);

        return $capacityNumber.$convertToUnit;
    }
}