<?php
/**
 * EBlueprintWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Blueprint Widget
 *
 * EBlueprintWidget widget insert a Blueprint CSS Framework in your layout.
 *
 * Insert widget in your layout, after head-tag.
 * <pre>
 * $this->widget('ext.yiiext.widgets.blueprint.EBlueprintWidget');
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.2
 * @package yiiext.widgets.blueprint
 */
class EBlueprintWidget extends CWidget
{
	// Url to vendors css-files.
	private static $baseUrl;

	public function setBaseUrl($url)
	{
		self::$baseUrl=$url;
	}
	public function getBaseUrl()
	{
		return self::$baseUrl;
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
	public static function getCssFiles($debug=false)
	{
		if(self::$baseUrl===null)
			self::$baseUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/vendors/joshuaclayton-blueprint-css-5d113e9/blueprint');

		if($debug)
		{
			return array(
				array(self::$baseUrl.'/src/reset.css','screen,projection'),
				array(self::$baseUrl.'/src/typography.css','screen,projection'),
				array(self::$baseUrl.'/src/grid.css','screen,projection'),
				array(self::$baseUrl.'/src/forms.css','screen,projection'),
				array(self::$baseUrl.'/src/print.css','print'),
				array(self::$baseUrl.'/src/ie.css','screen,projection','8'),
			);
		}
		else
		{
			return array(
				array(self::$baseUrl.'/screen.css','screen,projection'),
				array(self::$baseUrl.'/print.css','print'),
				array(self::$baseUrl.'/ie.css','screen,projection','8'),
			);
		}
	}
}
