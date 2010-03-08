<?php
/**
 * EFile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFile extends CComponent {
    private static $_files = array();   // file path => EFile

    private $_filePath;                 // file path
    private $_md;                       // meta data
    private $_children = array();       // children files
    private $_parent;                   // parent file

    public function __construct($filePath) {
        $this->setFilePath($filePath);
    }

    public static function getInstance($filePath) {
        if (!isset(self::$_files[$filePath])) {
            $file = self::$_files[$filePath] = new EFile($filePath);
            $file->_md = new EFileMetaData($file);
        }
        return self::$_files[$filePath];
    }

    public function setFilePath($filePath) {
        $this->_filePath = EFileHelper::realPath($filePath);
        if ($this->_filePath === FALSE) {
            throw new CException(Yii::t('yiiext', 'File "{file}" not exists.',
                array('{file}' => $filePath)));
        }
        return $this;
    }

    public function getFilePath() {
        return $this->_filePath;
    }

    public function getMetaData() {
        if ($this->_md === NULL) {
			$this->_md = self::getInstance($this->_filePath)->_md;
        }
        return $this->_md;
    }

    public function getParent() {
        if ($this->_parent === NULL) {
			$this->_parent = self::getInstance($this->getMetaData()->dirName);
        }
        return $this->_parent;
    }

    public function getFiles($filter = '*') {

    }
}

/**
 * EFileMetaData represents the meta-data for an EFile class.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
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
            $this->__get($attribute);
        }
    }
    public function __get($attribute) {
        if (!in_array($attribute, $this->_attributes)) {
            return NULL;
        }
        if (!isset($this->_metaData[$attribute])) {
            $getter = 'get' . $attribute;
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
