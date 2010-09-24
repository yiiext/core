<?php
/**
 * EFancyboxWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.widgets.fancybox
 */
Yii::import('ext.yiiext.widgets.fancybox.*');
class EFancyboxWidget extends CWidget
{
	/**
	 * @var EFancyboxConfiguration
	 */
	protected $_settings = NULL;
	
	protected $_enableMouseWheel = FALSE;
	protected $_selector = '"a[href$=\'.jpg\'],a[href$=\'.png\'],a[href$=\'.gif\']"'; //a:has(img)
	
	/**
	 * Group for fancybox. For all selected tags will be added `rel` option. 
	 */
	public $group = 'gallery';

	public function __construct($owner = NULL)
	{
		$this->_settings = new EFancyboxConfiguration(
			NULL,
			dirname(__FILE__) . '/config/defaults.php',
			dirname(__FILE__) . '/config/conditions.php'
		);
		parent::__construct($owner);
	}
	public function __get($name)
	{
		if ($this->_settings->hasValidKey($name))
		{
			return $this->_settings->itemAt($name);
		}
		return parent::__get($name);
	}
	public function __set($name, $value)
	{
		if ($this->_settings->hasValidKey($name))
		{
			$this->_settings->add($name, $value);
		}
		else
			parent::__set($name, $value);
	}
	/**
	 * Get settings array.
	 * @return array
	 */
	protected function getSettings()
	{
		return $this->_settings->toArray();
	}
	/**
	 * Set settings array.
	 * @param mixed
	 */
	protected function setSettings($data)
	{
		$this->_settings->copyFrom($data);
	}
	/**
	 * @return boolean
	 */
	protected function getEnableMouseWheel()
	{
		return $this->_enableMouseWheel;
	}
	/**
	 * Enable "mouse-wheel" to navigate throught gallery items.
	 * @param boolean
	 */
	protected function setEnableMouseWheel($value)
	{
		$this->_enableMouseWheel = ($value === TRUE ? TRUE : FALSE);
	}
	/**
	 * Get selector for generate fancybox.
	 * @return string
	 */
	protected function getSelector()
	{
		return $this->_selector;
	}
	/**
	 * Set selector for generate fancybox.
	 * @param string Selector for fancybox.
	 */
	protected function setSelector($value)
	{
		$this->_selector = CJavaScript::encode($value);
	}
	/**
	 * Run widget.
	 */
	public function run()
	{
		$dir = dirname(__FILE__).'/vendors/fancybox';
		$baseUrl = Yii::app()->getAssetManager()->publish($dir);

		$clientScript = Yii::app()->getClientScript();
		$clientScript->registerCssFile($baseUrl . '/jquery.fancybox-1.3.1.css', 'screen, projection');

		$clientScript->registerCoreScript('jquery');

		if ($this->transitionIn == 'elastic' || $this->transitionOut == 'elastic')
			$clientScript->registerScriptFile($baseUrl . '/jquery.easing-1.3.pack.js');
		if ($this->enableMouseWheel)
			$clientScript->registerScriptFile($baseUrl . '/jquery.mousewheel-3.0.2.pack.js');

		$clientScript->registerScriptFile($baseUrl . (YII_DEBUG ? '/jquery.fancybox-1.3.1.js' : '/jquery.fancybox-1.3.1.pack.js'));

		$clientScript->registerScript(
			'fbRun_' . md5($this->selector),
			'$(' . $this->selector . ')' . ($this->group !== NULL ? '.attr("rel", "' . $this->group . '")' : '') . '.fancybox(' . $this->_settings->getJSON() . ');',
			CClientScript::POS_READY
		);
	}
	/**
	 * Generate HTML code, image tag wrapped with link tag. 
	 * @static
	 * @param string Image url.
	 * @param string Alt text for image.
	 * @param array HTML options for image tag.
	 * @param array HTML options for link tag.
	 * @return string
	 */
	public static function image($src, $alt = '', $htmlOptions = array(), $linkHtmlOptions = array(),$link='')
	{
		!empty($link) OR $link=$src;
		isset($linkHtmlOptions['title']) OR $linkHtmlOptions['title'] = $alt;
		isset($htmlOptions['title']) OR $htmlOptions['title'] = $alt;
		return CHtml::link(CHtml::image($src, $alt, $htmlOptions), $link, $linkHtmlOptions);
	}
	/**
	 * Shows loading animation.
	 * @static
	 * @return string
	 */
	public static function showActivity()
	{
		return '$.fancybox.showActivity();';
	}
	/**
	 * Hides loading animation.
	 * @static
	 * @return string
	 */
	public static function hideActivity()
	{
		return '$.fancybox.hideActivity();';
	}
	/**
	 * Displays the next gallery item.
	 * @static
	 * @return string
	 */
	public static function next()
	{
		return '$.fancybox.next();';
	}
	/**
	 * Displays the previous gallery item.
	 * @static
	 * @return string
	 */
	public static function prev()
	{
		return '$.fancybox.prev();';
	}
	/**
	 * Displays item by index from gallery.
	 * @static
	 * @return string
	 */
	public static function pos()
	{
		return '$.fancybox.pos();';
	}
	/**
	 * Cancels loading content.
	 * @static
	 * @return string
	 */
	public static function cancel()
	{
		return '$.fancybox.cancel();';
	}
	/**
	 * Hides FancyBox. Within an iframe use - parent.$.fancybox.close();
	 * @static
	 * @return string
	 */
	public static function close()
	{
		return '$.fancybox.close();';
	}
	/**
	 * Auto-resizes FancyBox height to match height of content.
	 * @static
	 * @return string
	 */
	public static function resize()
	{
		return '$.fancybox.resize();';
	}
	/**
	 * Centers FancyBox in viewport.
	 * @static
	 * @return string
	 */
	public static function center()
	{
		return '$.fancybox.center();';
	}
}
