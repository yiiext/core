<?php
/**
 * CycleWidget
 *
 * @version 0.02
 * @author creocoder <creocoder@gmail.com>
 */
class ECycleWidget extends CWidget
{
	public $scriptUrl;
	public $scriptFile;
	public $scriptType='all';
	public $htmlOptions=array();
	public $options=array();
	public $tagName='div';

	public function init()
	{
		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->getId();

		if($this->scriptUrl===null)
			$this->scriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets');

		if($this->scriptFile===null)
		{
			switch($this->scriptType)
			{
				case 'lite':
					$this->scriptFile=YII_DEBUG ? '/jquery.cycle.lite.js' : '/jquery.cycle.lite.min.js';
					break;
				case 'normal':
					$this->scriptFile=YII_DEBUG ? '/jquery.cycle.js' : '/jquery.cycle.min.js';
					break;
				default:
					$this->scriptFile=YII_DEBUG ? '/jquery.cycle.all.js' : '/jquery.cycle.all.min.js';
					break;
			}
		}

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
		$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#$id').cycle($options);");
	}
}