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
	public $scriptUrl;
	
	public function init()
	{
		if($this->scriptUrl===null)
			$this->scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/vendors/joshuaclayton-blueprint-css-9be6857/blueprint');
	}
	public function run()
	{
		if(YII_DEBUG)
		{
			echo CHtml::cssFile($this->scriptUrl.'/src/reset.css','screen,projection')."\n";
			echo CHtml::cssFile($this->scriptUrl.'/src/typography.css','screen,projection')."\n";
			echo CHtml::cssFile($this->scriptUrl.'/src/grid.css','screen,projection')."\n";
			echo CHtml::cssFile($this->scriptUrl.'/src/forms.css','screen,projection')."\n";
			echo CHtml::cssFile($this->scriptUrl.'/src/print.css','print')."\n";
			echo '<!--[if lt IE 8]>'."\n";
			echo CHtml::cssFile($this->scriptUrl.'/src/ie.css','screen,projection')."\n";
			echo '<![endif]-->'."\n";
		}
		else
		{
			echo CHtml::cssFile($this->scriptUrl.'/screen.css','screen,projection')."\n";
			echo CHtml::cssFile($this->scriptUrl.'/print.css','print')."\n";
			echo '<!--[if lt IE 8]>'."\n";
			echo CHtml::cssFile($this->scriptUrl.'/ie.css','screen,projection')."\n";
			echo '<![endif]-->'."\n";
		}
	}
}
