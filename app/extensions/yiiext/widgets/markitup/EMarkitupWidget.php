<?php
/**
 * MarkitupWidget
 *
 * @version 1.0
 * @author creocoder <creocoder@gmail.com>
 */
class EMarkitupWidget extends CInputWidget
{
    /**
     * URL where to look for markItUp assets
     * @var string
     */
	public $scriptUrl;

    /**
     * markItUp script name
     * jquery.markitup.js by default
     * @var string
     */
	public $scriptFile;

    /**
     * URL where to look for a skin
     * @var string
     */
	public $themeUrl;

    /**
     * markItUp skin name
     * simple and markitup are available by default
     * @var string
     */
	public $theme='simple';

    /**
     * URL where to look for a tag set
     * @var string
     */
	public $settingsUrl;

    /**
     * Tag set name
     * html and markdown are available by default
     * @var string
     */
	public $settings='html';

    /**
     * markItUp options
     * @see http://markitup.jaysalvat.com/documentation/
     * @var array
     */
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
			$this->scriptFile='jquery.markitup.js';

		$this->registerClientScript();

		if($this->hasModel())
			echo CHtml::activeTextArea($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::textArea($name,$this->value,$this->htmlOptions);
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
