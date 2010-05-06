<?php
/**
 * MarkitupWidget
 *
 * @version 0.01
 * @author creocoder <creocoder@gmail.com>
 */
class EMarkitupWidget extends CInputWidget
{
	public $scriptUrl;
	public $scriptFile;
	public $themeUrl;
	public $theme='simple';
	public $settingsUrl;
	public $settings='html';
	public $options=array();

	public function init()
	{
		list($name,$id)=$this->resolveNameId();

		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;

		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];
		else
			$this->htmlOptions['name']=$name;

		if($this->scriptUrl===null || $this->themeUrl===null || $this->settingsUrl===null)
		{
			if($this->scriptUrl===null)
				$this->scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets');

			if($this->themeUrl===null)
				$this->themeUrl=$this->scriptUrl.'/skins';

			if($this->settingsUrl===null)
				$this->settingsUrl=$this->scriptUrl.'/sets';
		}

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG ? 'jquery.markitup.js' : 'jquery.markitup.pack.js';

		$this->registerClientScript();

		if($this->hasModel())
			echo CHtml::activeTextArea($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::activeTextArea($name,$this->value,$this->htmlOptions);
	}

	public function registerClientScript()
	{
		$id=$this->htmlOptions['id'];

		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->scriptUrl.'/'.$this->scriptFile);
		$cs->registerScriptFile($this->settingsUrl.'/'.$this->settings.'/set.js');

		if(empty($this->options))
			$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').markItUp(mySettings);");
		else
		{
			$options=CJavaScript::encode($this->options);
			$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').markItUp(mySettings,$options);");
		}

		$cs->registerCssFile($this->themeUrl.'/'.$this->theme.'/style.css');
		$cs->registerCssFile($this->settingsUrl.'/'.$this->settings.'/style.css');
	}
}