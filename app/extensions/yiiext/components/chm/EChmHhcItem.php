<?php
/**
 * EChmHhcItem class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * EChmHhcItem.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChmHhcItem extends EChmFile
{
	public $name;			// What is displayed in the Contents.
	public $local;			// The file to load when the user selects the index item.
	public $url;			// Should not be present unless Local also is. In the HHW GUI it is an "Alternate URL". Not sure what it does though.
	public $imageNumber;	// The image to display for this item.
	public $new;			// When an auto image is being used this increases the image number by 1 so that this image [] is seemingly added to the image.
	public $comment;		// Dunno what this does.
	public $frameName;		// The frame to open the topic in.
	public $windowName;		// The window to open the topic in.
	public $type;			// Category::Type
	public $merge;

	protected $_children=array();
	
	public function __construct($config=NULL)
	{
		if(!isset($config['name']))
			throw new CException(Yii::t('yiiext','HHC Item Name is required.'));

		parent::__construct($config);
	}
	public function addChild($config=NULL)
	{
		$child=new self($config);
		if($child->local!==null)
			return $this->_children[]=$child;

		return FALSE;
	}
	public function getChildren()
	{
		return $this->_children;
	}
}
