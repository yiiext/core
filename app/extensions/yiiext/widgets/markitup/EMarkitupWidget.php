<?php
/**
 * EMarkitupWidget class file.
 *
 * @author creocoder <creocoder@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EMarkitupWidget adds {@link http://markitup.jaysalvat.com/ markitup redactor} as a form field widget.
 *
 * Usage:
 * <pre>
 * $this->widget('ext.yiiext.widgets.markitup.EMarkitupWidget',array(
 *     // you can either use it for model attribute
 *     'model'=>$my_model,
 *     'attribute'=>'my_field',
 *     // or just for input field
 *     'name'=>'my_input_name',
 *     // {@link http://markitup.jaysalvat.com/documentation/ redactor options}
 *     'options'=>array(
 *         //...
 *     ),
 * ));
 * </pre>
 *
 * @author creocoder <creocoder@gmail.com>
 * @version 1.1
 * @package yiiext.widgets.markitup
 * @link http://markitup.jaysalvat.com/
 */
class EMarkitupWidget extends CInputWidget
{
	/**
	 * @var string URL where to look for markItUp assets.
	 */
	public $scriptUrl;
	/**
	 * @var string markItUp script name.
	 * Defaults to jquery.markitup.js or jquery.markitup.min.js depend by YII_DEGUG.
	 */
	public $scriptFile;
	/**
	 * @var string URL where to look for a skin.
	 */
	public $themeUrl;
	/**
	 * @var string markItUp skin name. Defaults to simple.
	 */
	public $theme='simple';
	/**
	 * @var string URL where to look for a tag set.
	 */
	public $settingsUrl;
	/**
	 * @var string tag set name. Defaults to html.
	 */
	public $settings='html';
	/**
	 * @var array {@link http://markitup.jaysalvat.com/documentation/ redactor options}.
	 */
	public $options=array();

	/**
	 * Init widget.
	 */
	public function init()
	{
		list($this->name,$this->id)=$this->resolveNameId();

		if($this->scriptUrl===null)
			$this->scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);

		if($this->themeUrl===null)
			$this->themeUrl=$this->scriptUrl.'/skins';

		if($this->settingsUrl===null)
			$this->settingsUrl=$this->scriptUrl.'/sets';

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG ? 'jquery.markitup.js' : 'jquery.markitup.min.js';

		$this->registerClientScript();
	}
	/**
	 * Run widget.
	 */
	public function run()
	{
		if($this->hasModel())
			echo CHtml::activeTextArea($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::textArea($this->name,$this->value,$this->htmlOptions);
	}
	/**
	 * Register CSS and Scripts.
	 */
	protected function registerClientScript()
	{
		$id=$this->id;
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->scriptUrl.'/'.$this->scriptFile);
		$cs->registerScriptFile($this->settingsUrl.'/'.$this->settings.'/set.js');

		$options=CJavaScript::encode($this->options);
		$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').markItUp(mySettings,$options);");

		$cs->registerCssFile($this->themeUrl.'/'.$this->theme.'/style.css');
		$cs->registerCssFile($this->settingsUrl.'/'.$this->settings.'/style.css');
	}
}
