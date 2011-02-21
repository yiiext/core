<?php
/**
 * ESwfobjectWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * ESwfobjectWidget embedding Adobe Flash Player content using SWFObject 2.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.widgets.swfobject
 * @link http://code.google.com/p/swfobject/
 */
class ESwfObjectWidget extends CWidget
{
	/**
	* @var string the tag name. It used flash container.
	*/
	public $tag='div';
	/**
	 * @var array html options for container.
	 */
	public $htmlOptions=array();
	/**
	 * @var string URL where to look assets.
	 */
	public $assetsUrl;
	/**
	 * @var string script name.
	 */
	public $scriptFile;
	/**
	 * @var string
	 */
	public $swfUrl='';
	/**
	 * @var int
	 */
	public $width;
	/**
	 * @var int
	 */
	public $height;
	/**
	 * @var string
	 */
	public $version='8';
	/**
	 * @var string|boolean
	 */
	public $expressInstallSwfurl=false;
	/**
	 * @var array
	 */
	public $flashvars=array();
	/**
	 * @var array
	 */
	public $params=array();
	/**
	 * @var array
	 */
	public $attributes=array();
	/**
	 * @var string|boolean
	 */
	public $callbackFn=false;
	/**
	* @var integer the position of the JavaScript code.
	* @see CClientScript::registerScriptFile()
	*/
	public $scriptPosition=CClientScript::POS_READY;

	/**
	 * Init widget.
	 */
	public function init()
	{
		if(empty($this->swfUrl))
			throw new CException('Invalid swf url.');

		if(empty($this->width))
			throw new CException('Invalid swf width.');

		if(empty($this->height))
			throw new CException('Invalid swf height.');

		if($this->assetsUrl===null)
			$this->assetsUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);

		if($this->scriptFile===null)
			$this->scriptFile=YII_DEBUG?'swfobject.v2.2.js':'swfobject.v2.2.min.js';

		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->getId();
		else
			$this->setId($this->htmlOptions['id']);

		$this->registerClientScript();

		echo CHtml::openTag($this->tag,$this->htmlOptions);
		$this->beginClip(__CLASS__.$this->getId());
	}
	/**
	 * Run widget.
	 */
	public function run()
	{
		$this->endClip(__CLASS__.$this->getId());
		$altText=$this->getController()->getClips()->itemAt(__CLASS__.$this->getId());
		if(empty($altText))
			$altText=Yii::t('yiiext','Flash cannot show, please update your player.');
		echo $altText;
		echo CHtml::closeTag($this->tag);
	}
	/**
	 * @return void
	 * Register CSS and Script.
	 */
	protected function registerClientScript()
	{
		$cs=Yii::app()->getClientScript();
		$cs->registerScriptFile($this->assetsUrl.'/'.$this->scriptFile);
		$cs->registerScript(__CLASS__.'#'.$this->getId(),
		'swfobject.embedSWF('.
			CJavaScript::encode($this->swfUrl).','.
			CJavaScript::encode($this->getId()).','.
			CJavaScript::encode($this->width).','.
			CJavaScript::encode($this->height).','.
			CJavaScript::encode(strval($this->version)).','.
			CJavaScript::encode($this->expressInstallSwfurl).','.
			CJavaScript::encode($this->flashvars).','.
			CJavaScript::encode($this->params).','.
			CJavaScript::encode($this->attributes).','.
			CJavaScript::encode($this->callbackFn).
		');',$this->scriptPosition);
	}
}
