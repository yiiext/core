<?php
/**
 * EChmWindow class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/**
 * EChmWindow.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.chm
 */
class EChmWindow extends CComponent
{
	public $id;
	public $title;
	public $tocPath;
	public $indexPath;
	public $defaultFile;
	public $homeFile;
	public $jump1File;
	public $jump1Caption;
	public $jump2File;
	public $jump2Caption;
	public $navigationStyle;			// A bit feild of navigation pane styles.
	public $navigationWidth=300;		// Width of the navigation pane in pixels.
	public $buttons='0x104e';			// A bit field of the buttons to show.
	public $position='[60,60,810,560]';	// Initial position of the window on the screen: [left, top, right, bottom].
	public $style;						// Style Flags. As set in the Win32 SetWindowLong & CreateWindow APIs.
	public $extStyle;					// Extended Style Flags. As set in the Win32 SetWindowLong & CreateWindowEx APIs.
	public $windowState;				// Window show state. As set in the Win32 ShowWindow API. SW_HIDE works well - don't use it.
										// The following are buggy in HH 1.31: SW_SHOWMINIMIZED, SW_MINIMIZE, SW_SHOWMINNOACTIVE - grey toolbars, no nav pane or HTML display when unminimized;
										// SW_MAXIMIZE - maximizes the navigation pane, if present; SW_FORCEMINIMIZE - works same as SW_HIDE, when should just minimize.
	public $navigationClosed=0;			// Whether or not the navigation pane is initially closed. 1 = closed, 0 = open
	public $defaultTab=0;				// The default navigation pane. 0 = TOC, 1 = Index, 2 = Search, 3 = Favorites, 4 = History (not implemented by HH), 5 = Author, 11-19 = Custom panes.
	public $tabsPosition=0;				// Where the navigation pane tabs should be. 0 = Top, 1 = Left, 2 = Bottom & anything else makes the tabs appear to be behind the pane.
	public $notifyId=0;					// ID to send in WM_NOTIFY messages.

	protected $_parent;
	
	public function __construct($parent,$config=NULL)
	{
		$this->setParent($parent);
		$config=new CConfiguration($config);
		$config->applyTo($this);
	}
	public function toString()
	{
		if($this->tocPath===null)
			$this->tocPath=$this->getParent()->contentsFile;
		if($this->indexPath===null)
			$this->indexPath=$this->getParent()->indexFile;

		return "{$this->id}=\"{$this->title}\",\"{$this->tocPath}\",\"{$this->indexPath}\",\"{$this->defaultFile}\",\"{$this->homeFile}\",".
				"\"{$this->jump1File}\",\"{$this->jump1Caption}\",\"{$this->jump2File}\",\"{$this->jump2Caption}\",{$this->navigationStyle},".
				"{$this->navigationWidth},{$this->buttons},{$this->position},{$this->style},{$this->extStyle},{$this->windowState},".
				"{$this->windowState},{$this->navigationClosed},{$this->defaultTab},{$this->tabsPosition},{$this->notifyId}";
	}
	public function getParent()
	{
		return $this->_parent;
	}
	public function setParent($parent)
	{
		$this->_parent=$parent;
	}
}
