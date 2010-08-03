<?php
/**
 * EChmHhkFile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * EChmHhkFile.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChmHhkFile extends EChmFile
{
	public $frameName;	// This is used where the topics should open in a specific HTML frame.
	public $windowName;	// This is used where the topics should open in a specific window.
	public $font;		// Font name, font size, character set.

    protected $_items=array();

    public function addItem($config=NULL)
    {
        return $this->_items[]=new EChmHhkItem($config);
    }
    public function getItems()
    {
        return $this->_items;
    }
}
