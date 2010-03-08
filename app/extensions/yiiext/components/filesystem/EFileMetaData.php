<?php
/**
 * EFileMetaData class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class EFileMetaData {
    protected $_attributes = array(
        'name', 'path', 'dirName', 'type', 'extension', 'mime', 'isDir',
        'size', 'permissions', 'modifiedTime', 'accessedTime',
    );
    protected $_file;
    protected $_filePath;
    protected $_metaData = array();

    public function __construct($file) {
        $this->_file = $file;
        $this->_filePath = $file->getFilePath();
        clearstatcache(TRUE, $this->_filePath);
        foreach ($this->_attributes as $attribute) {
            $this->getMetaData($attribute);
        }
    }
    public function getMetaData($attribute) {
        if (!isset($this->_metaData[$attribute])) {
            $getter='get' . $attribute;
		    if (method_exists($this, $getter)) {
                $this->_metaData[$attribute] = $this->$getter(); 
            }
            else {
                throw new CException(Yii::t('yiiext', 'Attribute {attribute} for file "{file}" not exists.',
                    array('{attribute}' => $attribute, '{file}' => $this->_filePath)));
            }
        }
        return $this->_metaData[$attribute];
    }
    public function toArray() {
        return $this->_metaData;
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
