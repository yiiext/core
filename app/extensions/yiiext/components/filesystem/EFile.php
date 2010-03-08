<?php
/**
 * EFile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
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

}
