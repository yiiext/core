<?php
/**
 * EFileValidator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileValidators {
    /**
     *
     */
    private $_validators;
    /**
     * @param  $rules
     * @return void
     */
    public function __construct($rules) {
        $this->_validators = $this->createValidators($rules);
    }
    /**
     * @return array
     */
    public function getValidators() {
        return $this->_validators;
    }
    /**
     * @throws CException
     * @param  $rules
     * @return array
     */
    public function createValidators($rules) {
        $validators = array();
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (isset($rule[0])) { // validator name
                    $validators[] = EFileValidator::createValidator($rule[0], array_slice($rule,1));
                }
                else {
                    throw new CException(Yii::t('yiiext','Invalid validation rule. The rule must specify the validator name.'));
                }
            }
        }
        return $validators;
    }
    /**
     * @param  $file
     * @return bool
     */
    public function validate($file) {
        foreach ($this->getValidators() as $validator) {
            if (!$validator->validate($file)) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
/**
 * EFileValidator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
abstract class EFileValidator extends CComponent {
    /**
     * @var array list of built-in validators (name=>class)
     */
    public static $builtInValidators = array(
        'size' => 'EFileSizeValidator',
        'name' => 'EFileNameValidator',
        'extension' => 'EFileExtensionValidator',
    );
    /**
     *
     */
    public $message;
    /**
     * @static
     * @param  $name
     * @param  $parameters
     * @return
     */
    public static function createValidator($name, $parameters) {
        if (isset(self::$builtInValidators[$name])) {
            $className = self::$builtInValidators[$name];
        }
        else {
            $className = Yii::import($name, true);
        }
        $validator = new $className;
        foreach($parameters as $name => $value) {
            $validator->$name = $value;
        }
        return $validator;
    }
    /**
     * Validates the specified file.
     * @param EFile the file being validated
     */
    abstract public function validate(EFile $file);
}
/**
 * EFileSizeValidator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileSizeValidator extends EFileValidator {
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
    public function validate(EFile $file) {
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
     * @param  $value
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
 * EFileExtensionValidator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileExtensionValidator extends EFileValidator {
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
    public function validate(EFile $file) {
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
 * EFileNameValidator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileNameValidator extends EFileValidator {
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
    public function validate(EFile $file) {
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
