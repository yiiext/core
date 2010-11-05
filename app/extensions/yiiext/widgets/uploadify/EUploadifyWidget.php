<?php
/**
 * EUploadifyWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * EUploadifyWidget adds {@link http://www.uploadify.com/ uploadify jQuery plugin} as a form field widget.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 1.6
 * @package yiiext.widgets.uploadify
 * @link http://www.uploadify.com/
 */
class EUploadifyWidget extends CInputWidget  {
	/**
	 * @var string URL where to look uploadify assets.
	 */
	public $assetsUrl;
	/**
	 * @var string uploadify script name.
	 */
	public $scriptFile;
	/**
	 * @var string uploadify stylesheet.
	 */
	public $cssFile;
	/**
	 * @var string|null the name of the POST parameter where save session id.
	 * Or null to disable sending session id. Use EForgerySessionFilter to load session by id from POST.
	 * Defaults to null.
	 * @see EForgerySessionFilter
	 */
	public $sessionParam;
	/**
	 * @var array extension options. For more info read {@link http://www.uploadify.com/documentation/ documentation}
	 */
	public $options=array();

	/**
	 * Init widget.
	 */
	public function init()
	{
		list($this->name,$this->id)=$this->resolveNameId();

		if($this->assetsUrl===null)
			$this->assetsUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG ? 'jquery.uploadify.v2.1.3.js' : 'jquery.uploadify.v2.1.3.min.js';

		if($this->cssFile===null)
			$this->cssFile='uploadify.css';

		if(!isset($this->options['uploader']))
			$this->options['uploader']=$this->assetsUrl.'/uploadify.swf';
		
		if(!isset($this->options['cancelImg']))
			$this->options['cancelImg']=$this->assetsUrl.'/cancel.png';

		if(!isset($this->options['expressInstall']))
			$this->options['expressInstall']=$this->assetsUrl.'/expressInstall.swf';

		if(!isset($this->options['script']))
			$this->options['script']=$this->assetsUrl.'/uploadify.php';

		// send session id with post
		if($this->sessionParam!==null && !isset($this->options['scriptData'][$this->sessionParam]))
			$this->options['scriptData'][$this->sessionParam]=Yii::app()->getSession()->getSessionId();

		// TODO: Csrf Validation
		// С этим пока проблема. Т.к. flash upload не посылает куки из-за политики безопасности.
		// if(Yii::app()->getRequest()->enableCsrfValidation && (!isset($this->options['method']) || $this->options['method']=='POST'))
		//  	$this->options['scriptData'][Yii::app()->getRequest()->csrfTokenName]=Yii::app()->getRequest()->getCsrfToken();

		// if(!isset($this->options['checkScript']))
		//  	$this->options['checkScript']=$this->assetsUrl.'/check.php';

		// fileDesc is required if fileExt set.
		if(!empty($this->options['fileExt']) && empty($this->options['fileDesc']))
			$this->options['fileDesc']=Yii::t('yiiext','Supported files ({fileExt})',array('{fileExt}'=>$this->options['fileExt']));

		// Generate fileDataName for linked with model attribute.
		$this->options['fileDataName']=$this->name;

		$this->registerClientScript();
	}
	/**
	 * Run widget.
	 */
	public function run()
	{
		if($this->hasModel())
			echo CHtml::activeFileField($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::textArea($this->name,$this->value,$this->htmlOptions);
	}
	/**
	 * @return void
	 * Register CSS and Script.
	 */
	protected function registerClientScript()
	{
		$cs=Yii::app()->getClientScript();
		$cs->registerCssFile($this->assetsUrl.'/'.$this->cssFile);
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->assetsUrl.'/'.$this->scriptFile);
		$cs->registerScriptFile($this->assetsUrl.'/swfobject.js');
		$cs->registerScript(__CLASS__.'#'.$this->id,'jQuery("#'.$this->id.'").uploadify('.CJavaScript::encode($this->options).');',CClientScript::POS_READY);
	}
}
