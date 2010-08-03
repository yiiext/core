<?php
/**
 * EFile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.2
 * @package yiiext.filesystem
 */
class EFile extends CComponent {
	private static $_files = array();	// file path => EFile

	private $_filePath;					// file path
	private $_md;						// meta data
	private $_children;					// children files
	private $_parent;					// parent file

	public function __construct($filePath) {
		if (!file_exists($filePath)) {
			throw new CException(Yii::t('yiiext', 'File "{file}" not exists.', array('{file}' => $filePath)));
		}
		$this->_filePath = $filePath;
	}

	public function __get($name) {
		if (in_array($name, EFileMetaData::$attributeLabels))
			return $this->getMetaData()->getAttribute($name);

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
		if ($this->_md === NULL)
			$this->_md = self::getInstance($this->_filePath)->_md;

		return $this->_md;
	}

	public function getParent() {
		if ($this->_parent === NULL)
			$this->_parent = self::getInstance($this->getMetaData()->dirName);

		return $this->_parent;
	}

	public function getFiles() {
		if ($this->_children === NULL)
			$this->_children = $this->find(NULL, 0);
		
		return $this->_children;
	}

	public static function findRecursive($dir, $filters = NULL, $depth = -1, $limit = 0) {
		if (!($filters instanceof EFileFilters))
			$filters = new EFileFilters($filters);
		
		$list = new CList;
		//TODO: $dir = new DirectoryIterator(dirname(__FILE__)); foreach ($dir as $fileinfo) {}
		$handle = opendir($dir);
		while (($fileName = readdir($handle)) !== FALSE) {
			if ($limit > 0 && $list->count >= $limit)
				break;
			if ($fileName === '.' || $fileName === '..')
				continue;
			$file = EFile::getInstance($dir . DIRECTORY_SEPARATOR . $fileName);
			//TODO: подумать о сортировке прямо в цикле поиска
			if ($filters->run($file)) {
				if ($file->isDir && $depth)
					$list->mergeWith(self::findRecursive($file->path, $filters, $depth - 1, $limit - $list->count));
				else
					$list->add($file);
			}
		}
		closedir($handle);
		return $list;
	}

	public function find($filters = NULL, $depth = -1, $limit = 0) {
		return $this->isDir ? self::findRecursive($this->path, $filters, $depth, $limit) : NULL;
	}
}
