<?php
/**
 * EFileMetaData represents the meta-data for an EFile class.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */

class EFileMetaData {
    public $attributes = array();

    protected $_file;
    protected $_filePath;
    protected $defaultAttributes = array(
        'name' => NULL,
        'path' => NULL,
        'dirName' => NULL,
        'type' => NULL,
        'extension' => NULL,
        'mime' => NULL,
        'isDir' => NULL,
        'size' => NULL,
        'permissions' => NULL,
        'modifiedTime' => NULL,
        'accessedTime' => NULL,
    );

    public function __construct($file, $loadAttributes = TRUE) {
        $this->_file = $file;
        $this->_filePath = $file->getFilePath();
        clearstatcache(TRUE, $this->_filePath);
        if ($loadAttributes) {
            $this->loadAttributes();
        }
    }
    public function loadAttributes() {
        foreach ($this->defaultAttributes as $attribute => $value) {
            $this->getAttribute($attribute);
        }
        return $this;
    }
    public function getAttribute($attribute) {
        if (!isset($this->attributes[$attribute])) {
            $getter = 'get' . $attribute;
            if (method_exists($this, $getter)) {
                $this->attributes[$attribute] = $this->$getter();
            }
            else {
                return NULL;
            }
        }
        return $this->attributes[$attribute];
    }
    public function getIsDir() {
        return is_dir($this->_filePath);
    }
    public function getName() {
        return basename($this->_filePath);
    }
    public function getPath() {
        return $this->_filePath;
    }
    public function getDirName() {
        return dirname($this->_filePath);
    }
    public function getType() {
        return filetype($this->_filePath);
    }
    public function getExtension() {
        return EFileHelper::fileExtension($this->_filePath);
    }
    public function getMime() {
        return EFileHelper::getMimeType($this->_filePath);
    }
    public function getSize() {
        return EFileHelper::fileSize($this->_filePath);
    }
    public function getPermissions() {
        return fileperms($this->_filePath);
    }
    public function getModifiedTime() {
        return filemtime($this->_filePath);
    }
    public function getAccessedTime() {
        return fileatime($this->_filePath);
    }
}
