<?php
/**
 * EFileCriteria class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileCriteria {
    const T = 1024;
    /**
     * @var string the mask for files names.
     * Defaults to '*', meaning all files.
     */
    public $pattern = '*';
    /**
     * @var array the files attributes of search.
     * @see EFileMetaData::attributeLabels
     */
    protected $condition = array();
    /**
     * @var integer recursion depth. It defaults to -1.
	 * Depth -1 means searching for all directories and files under the directory;
	 * Depth 0 means searching for only the files DIRECTLY under the directory;
	 * Depth N means searching for those directories that are within N depth.
     */
    public $depth = -1;
    /**
     * @var integer maximum number of files to be returned. If less than 0, it means no limit.
     */
    public $limit = 0;
    /**
     * Constructor.
     * @param mixed array criteria initial property values (indexed by property name)
     * or string the path of config file.
     */
    public function __construct($data = array()) {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function __set($name, $value) {
        if (in_array($name, EFileMetaData::$attributeLabels)) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
            else {
                $this->condition[$name] = $value;
            }
            return;
        }
        throw new CException(Yii::t('yiiext','Property "{class}.{property}" is not defined.',
			array('{class}'=>get_class($this), '{property}'=>$name)));
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        if (in_array($name, EFileMetaData::$attributeLabels)) {
            return isset($this->condition[$name]) ? $this->condition[$name] : NULL;
        }
        throw new CException(Yii::t('yiiext','Property "{class}.{property}" is not defined.',
			array('{class}'=>get_class($this), '{property}'=>$name)));
    }

    protected function setSize($value) {
        $value = str_replace(' ', '', $value);
        $prefix = $value[0];
        $suffix = $value[strlen($value) - 1];
        $v0 = 1; $v1 = -1;
        if ((int)$prefix > 0) {
            $prefix = '='; $v0 = 0;
        }
        if ((int)$suffix > 0) {
            $suffix = ''; $v1 = 0;
        }
        $value = $v1 ? substr($value, $v0, $v1) : substr($value, $v0);
        if (!isset($this->condition['size'])) {
            $this->condition['size'] = array();
        }
        $this->condition['size'][] = array($prefix, $value, $suffix);
    }

    public function mergeWith($criteria, $useAnd = TRUE) {
        $and = $useAnd ? 'AND' : 'OR';
        if (!($criteria instanceof EFileCriteria)) {
            $criteria = new self($criteria);
        }
        
        if ($criteria->depth != -1) {
            $this->depth = $criteria->depth;
        }

        if ($criteria->limit > 0) {
            $this->limit = $criteria->limit;
        }
        
        return $this;
    }

    /**
	 * @return array the array representation of the criteria
	 */
	public function toArray() {
		$result = array();
		foreach (array_merge(array('pattern', 'depth', 'limit'), EFileMetaData::$attributeLabels) as $name)
			$result[$name] = $this->$name;
		return $result;
	}
}
