<?php
/**
 * ESimpleModalWidget class file.
 *
 * Add {@link http://www.ericmmartin.com/projects/simplemodal/ SimpleModal jQuery plugin}.
 *
 * @author Makarov Alexander
 * @version 1.2
 * @package yiiext.widgets.simplemodal
 * @link http://www.ericmmartin.com/projects/simplemodal/
 */
class ESimpleModalWidget extends CWidget
{
	/**
	 * @var string URL where to look assets.
	 */
	public $assetsUrl;
	/**
	 * @var string script filename.
	 */
	public $scriptFile;
	/**
	 * @var string|boolean css filename.
	 */
	public $cssFile;
	/**
	 * @var string|null trigger element selector.
	 */
	public $selector;
	/**
	 * @var string the dialog HTML content.
	 */
	public $content;
	/**
	 * @var array extension options. For more details read {@link http://www.ericmmartin.com/projects/simplemodal/ documentation}
	 */
	public $options=array();

	/**
	 * Init widget.
	 * @return void
	 */
	function init()
	{
		if($this->assetsUrl===null)
			$this->assetsUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG ? 'jquery.simplemodal-1.4.1.js' : 'jquery.simplemodal-1.4.1.min.js';

		if($this->cssFile===null)
			$this->cssFile='simplemodal.css';

		$this->registerClientScript();
		echo CHtml::openTag('div',array('id'=>$this->getId(),'style'=>'display:none;'));
	}
	/**
	 * Run widget.
	 * @return void
	 */
	function run()
	{
		if(is_string($this->content))
			echo $this->content;
		echo CHtml::closeTag('div');
	}
	/**
	 * Register CSS and Script.
	 * @return void
	 */
	protected function registerClientScript()
	{
		$cs=Yii::app()->getClientScript();
		if($this->cssFile!==false)
			$cs->registerCssFile($this->assetsUrl.'/'.$this->cssFile);
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->assetsUrl.'/'.$this->scriptFile);
		$cs->registerScript($this->getId(),'$("'.$this->selector.'").click(function(e){e.preventDefault(); $("#'.$this->getId().'").modal('.CJavaScript::encode($this->options).');});');
	}
}
