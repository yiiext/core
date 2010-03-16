<?php
/**
 * EFileFilters class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileFilters {
	/**
	 *
	 */
	private $_filters;
	/**
	 * @param $rules
	 * @return void
	 */
	public function __construct($rules) {
		$this->_filters = $this->createFilters($rules);
	}
	/**
	 * @return array
	 */
	public function getFilters() {
		return $this->_filters;
	}
	/**
	 * @throws CException
	 * @param $rules
	 * @return array
	 */
	public function createFilters($rules) {
		$filters = array();
		if (is_array($rules)) {
			foreach ($rules as $rule) {
				if (isset($rule[0])) { // filter name
					$filters[] = EFileFilter::createFilter($rule[0], array_slice($rule,1));
				}
				else {
					throw new CException(Yii::t('yiiext','Invalid filter rule. The rule must specify the filter name.'));
				}
			}
		}

		return $filters;
	}
	/**
	 * @param $file
	 * @return bool
	 */
	public function run($file) {
		foreach ($this->getFilters() as $filter) {
			if (!$filter->run($file)) {
				return FALSE;
			}
		}
		
		return TRUE;
	}
}
/**
 * EFileFilter class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
abstract class EFileFilter extends CComponent {
	/**
	 * @var array list of built-in filters (name=>class)
	 */
	public static $builtInFilters = array(
		'size' => 'EFileSizeFilter',
		'name' => 'EFileNameFilter',
		'extension' => 'EFileExtensionFilter',
	);
	/**
	 *
	 */
	public $message;
	/**
	 * @static
	 * @param $name
	 * @param $parameters
	 * @return
	 */
	public static function createFilter($name, $parameters) {
		if (isset(self::$builtInFilters[$name])) {
			$className = self::$builtInFilters[$name];
		}
		else {
			$className = Yii::import($name, true);
		}
		$filter = new $className;
		foreach($parameters as $name => $value) {
			$filter->$name = $value;
		}

		return $filter;
	}
	/**
	 * Filter the specified file.
	 * @param EFile the file being filter
	 */
	abstract public function run(EFile $file);
}
/**
 * EFileSizeFilter class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileSizeFilter extends EFileFilter {
	/**
	 * @var integer the minimum number of bytes required for the file.
	 * Defaults to null, meaning no limit.
	 * @see tooSmall
	 */
	public $minSize;
	/**
	 * @var integer the maximum number of bytes required for the file.
	 * Defaults to null, meaning no limit.
	 * @see tooLarge
	 */
	public $maxSize;
	/**
	 * @var string the error message used when the file is too large.
	 * @see maxSize
	 */
	public $tooLarge;
	/**
	 * @var string the error message used when the file is too small.
	 * @see minSize
	 */
	public $tooSmall;
	/**
	 * @param EFile $file
	 * @return boolean
	 */
	public function run(EFile $file) {
		if ($file->isDir) {
			return TRUE;
		}
		if ($this->maxSize !== NULL && (int)$file->size > (int)$this->maxSize) {
			$message = $this->tooLarge !== NULL ? $this->tooLarge : Yii::t('yiiext','The file "{file}" is too large. Its size cannot be exceed than {limit} bytes.');

			return FALSE;
		}
		if ($this->minSize !== NULL && (int)$file->size < (int)$this->minSize) {
			$message = $this->tooSmall !== NULL ? $this->tooSmall : Yii::t('yiiext','The file "{file}" is too small. Its size cannot be smaller than {limit} bytes.');

			return FALSE;
		}

		return TRUE;
	}
	/**
	 * @param $value
	 * @return void
	 */
	protected function getSize($value) {
		$value = str_replace(' ', '', $value);
		$lastChar = $value[strlen($value) - 1];
		$digitPos = -1;
		if ((int)$lastChar > 0) {
			$lastChar = '';
			$digitPos = 0;
		}
		$value = $lastChar ? substr($value, 0, $lastChar) : $value;

		return array($value, $lastChar);
	}
}
/**
 * EFileExtensionFilter class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileExtensionFilter extends EFileFilter {
	/**
	 * @var mixed a list of file name extensions.
	 * This can be either an array or a string consisting of file extension names
	 * separated by space or comma (e.g. "gif, jpg").
	 * Extension names are case-insensitive. Defaults to null, meaning all file name
	 * extensions are allowed.
	 */
	public $extension;
	/**
	 * @var string the error message used when the file has an extension name
	 * that is not listed among {@link extensions}.
	 */
	public $wrongExtension;
	/**
	 * @var boolean
	 */
	public $skipDir = FALSE;
	/**
	 * @param EFile $file
	 * @return boolean
	 */
	public function run(EFile $file) {
		if ($this->skipDir && $file->isDir) {
			return TRUE;
		}
		if($this->extension !== NULL) {
			$extension = is_string($this->extension) ? preg_split('/[\s,]+/', strtolower($this->extension), -1, PREG_SPLIT_NO_EMPTY) : $this->extension;
			if(!in_array(strtolower($file->extension), $extension)) {
				$message = $this->wrongExtension !== NULL ? $this->wrongExtension : Yii::t('yiiext','The file extension "{extension}" wrong. Only files with these extensions are allowed: {extensions}.');

				return FALSE;
			}
		}

		return TRUE;
	}
}
/**
 * EFileNameFilter class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileNameFilter extends EFileFilter {
	/**
	 * @var mixed a list of file name extensions.
	 * This can be either an array or a string consisting of file extension names
	 * separated by space or comma (e.g. "gif, jpg").
	 * Extension names are case-insensitive. Defaults to null, meaning all file name
	 * extensions are allowed.
	 */
	public $pattern;
	/**
	 * @var string the error message used when the file has an extension name
	 * that is not listed among {@link extensions}.
	 */
	public $wrongPattern;
	/**
	 * @var boolean
	 */
	public $skipDir = FALSE;
	/**
	 * @param EFile $file
	 * @return boolean
	 */
	public function run(EFile $file) {
		if ($this->skipDir && $file->isDir) {
			return TRUE;
		}
		if(is_string($this->pattern)) {
			if(!fnmatch($this->pattern, $file->name)) {
				$message = $this->wrongPattern !== NULL ? $this->wrongPattern : Yii::t('yiiext','The file name wrong. Only files with these name patterm are allowed: {pattern}.');

				return FALSE;
			}
		}

		return TRUE;
	}
}
