<?php
/**
 * EChmHhkItem class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * EChmHhkItem.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChmHhkItem extends EChmFile
{
	public $name;			// What is displayed in the Contents.
	public $type;			// Category::Type
	public $local;			// The file to load when the user selects the index item.
	public $url;			// Should not be present unless Local also is. In the HHW GUI it is an "Alternate URL". Not sure what it does though.
	public $seeAlso;		// When the user double-clicks the index item the Index control will jump to the Index entry whose keyword is the value of this param. Mutually exclusive with the Local [URL] pair
	public $frameName;		// The frame to open the topic in.
	public $windowName;		// The window to open the topic in.
	public $comment;		// Dunno what this does.

	protected $_children=array();

	public function __construct($config=NULL)
	{
		if(!isset($config['name']))
			throw new CException(Yii::t('yiiext','HHK Item Name is required.'));

		parent::__construct($config);
	}
	public function addChild($config=NULL)
	{
		$child=new self($config);
		if($child->local!==null)
		{
			if($this->local===null)
				$this->local=$child->local;
			return $this->_children[]=$child;
		}

		return FALSE;
	}
	public function getChildren()
	{
		return $this->_children;
	}
}
