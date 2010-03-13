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
    private $_children;                 // children files
    private $_parent;                   // parent file

    public function __construct($filePath) {
        if (!file_exists($filePath)) {
            throw new CException(Yii::t('yiiext', 'File "{file}" not exists.',
                array('{file}' => $filePath)));
        }
        $this->_filePath = $filePath;
    }

    public function __get($name) {
        if (in_array($name, EFileMetaData::$attributeLabels)) {
            return $this->getMetaData()->getAttribute($name);
        }
        return parent::__get($name);
    }

    public static function getInstance($filePath) {
        $filePath = EFileHelper::realPath($filePath);
        if (!isset(self::$_files[$filePath])) {
            $file = self::$_files[$filePath] = new EFile($filePath);
            $file->_md = new EFileMetaData($file);
        }
        return self::$_files[$filePath];
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

    public function getFiles($ignoreCache = FALSE) {
        if ($ignoreCache || $this->_children === NULL) {
            $this->_children = new EFileFinder($this->path, array('depth' => 0));
        }
        return $this->_children;
    }
}
