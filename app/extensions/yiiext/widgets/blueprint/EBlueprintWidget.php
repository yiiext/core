<?php
/**
 * EBlueprintWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EBlueprintWidget widget insert a Blueprint CSS Framework in your layout.
 *
 * Insert widget in your layout, after head-tag.
 * <pre>
 * $this->widget('ext.yiiext.widgets.blueprint.EBlueprintWidget');
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.widgets.blueprint
 */
class EBlueprintWidget extends CWidget
{
	// Url to vendors css-files.
	private static $scriptUrl;
	
	public function setScriptUrl($url)
	{
		self::$scriptUrl=$url;
	}
	public function getScriptUrl()
	{
		return self::$scriptUrl;
	}
	public function init()
	{
		foreach(self::getCssFiles(YII_DEBUG) as $file)
		{
			if(isset($file[2]))
				echo '<!--[if lt IE '.$file[2].']>'."\n";
			echo CHtml::cssFile($file[0],isset($file[1]) ? $file[1] : '')."\n";
			if(isset($file[2]))
				echo '<![endif]-->'."\n";
		}
	}
	public static function getCssFiles($getSrc=false)
	{
		if(self::$scriptUrl===null)
			self::$scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/vendors/joshuaclayton-blueprint-css-9be6857/blueprint');
		
		if($getSrc)
		{
			return array(
				array(self::$scriptUrl.'/src/reset.css','screen,projection'),
				array(self::$scriptUrl.'/src/typography.css','screen,projection'),
				array(self::$scriptUrl.'/src/grid.css','screen,projection'),
				array(self::$scriptUrl.'/src/forms.css','screen,projection'),
				array(self::$scriptUrl.'/src/print.css','print'),
				array(self::$scriptUrl.'/src/ie.css','screen,projection','8'),
			);
		}
		else
		{
			return array(
				array(self::$scriptUrl.'/screen.css','screen,projection'),
				array(self::$scriptUrl.'/print.css','print'),
				array(self::$scriptUrl.'/ie.css','screen,projection','8'),
			);
		}
	}
}
