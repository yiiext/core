<?php
/**
 * EFormModelBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormModelBehavior attach instance of EForm for model for easy create form.
 *
 * Also add helper to generate array of default text elements based of {@link CModel::getAttributes()}.
 *
 * @author Maxim Furtuna
 * @version 0.1
 * @package yiiext.components.form
 */
require_once(dirname(__FILE__).'/EForm.php');
class EFormModelBehavior extends CModelBehavior
{
	/**
	 * @var array initial configuration for form.
	 */
	public $config=array();
	/**
	 * @var EForm main object
	 */
	protected $_form;
	
	/**
	 * Get main form.
	 * @return EForm
	 */
	public function getForm($config=array(),$owner=null)
	{
		if($this->_form===null)
		{
			if(!($owner instanceof CBaseController))
				$owner=Yii::app()->getController();
			$this->_form=new EForm(CMap::mergeArray($this->config,$config),null,$owner);
			$this->_form->setElements(array($this->getOwner()));
		}
		return $this->_form;
	}
	/**
	 * Render form.
	 */
	public function render()
	{
		echo $this->_form->render();
	}
	/**
	 * Get array elements based of {@link CModel::getAttributes()}.
	 * Type of all elements will be text.
	 * @todo generate other types for inputs
	 * @return array
	 */
	public function getFormElements()
	{
		$model=$this->getOwner();
		if(method_exists($model,'getFormElements'))
			return $model->getFormElements();
		else
		{
			$elements=array();
			foreach($model->getAttributes() as $attribute=>$value)
				$elements[$attribute]=array(
					'type'=>'text',
				);
			return $elements;
		}
	}
}
