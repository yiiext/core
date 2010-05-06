<?php
/**
 * CarouselWidget
 *
 * @version 0.01
 * @author creocoder <creocoder@gmail.com>
 */
class ECarouselWidget extends CWidget
{
	public $scriptUrl;
	public $scriptFile;
	public $themeUrl;
	public $theme='tango';
	public $htmlOptions=array();
	public $options=array();
	public $tagName='ul';
	public $cssFile='skin.css';

	public function init()
	{
		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->getId();

		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class']='jcarousel-skin-'.$this->theme;

		if($this->scriptUrl===null || $this->themeUrl===null)
		{
			if($this->scriptUrl===null)
				$this->scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets');

			if($this->themeUrl===null)
				$this->themeUrl=$this->scriptUrl.'/skins';
		}

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG ? '/jquery.jcarousel.js' : '/jquery.jcarousel.min.js';

		$this->registerClientScript();
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
	}

	public function run()
	{
		echo CHtml::closeTag($this->tagName)."\n";
	}

	public function registerClientScript()
	{
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->scriptUrl.'/'.$this->scriptFile);
		$id=$this->htmlOptions['id'];
		$options=empty($this->options) ? '' : CJavaScript::encode($this->options);
		$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').jcarousel($options);");

		if($this->cssFile!==false)
			$cs->registerCssFile($this->themeUrl.'/'.$this->theme.'/'.$this->cssFile);
	}
}