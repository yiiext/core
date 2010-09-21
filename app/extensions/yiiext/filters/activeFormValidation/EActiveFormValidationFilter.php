<?php
/**
 * EActiveFormValidationFilter class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EActiveFormValidationFilter performs validation of active form.
 *
 * To AJAX validation with {@link CActiveForm}, we can use this filter.
 * Add the following class code:
 * <pre>
 * public function filters()
 * {
 *     Yii::import('ext.yiiext.filters.activeFormValidation.EActiveFormValidationFilter');
 *     return array(
 *         array(
 *             'EActiveFormValidationFilter[ +|- Action1, Action2, ...]',
 *             'models'=>array('ModelClassToBeValidate',new OtherModel('update')),
 *             'formId'=>'form',
 *             'ajaxVar'=>'ajax,
 *         ),
 *     );
 * }
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.2
 * @package yiiext.filters.activeFormValidation
 */
class EActiveFormValidationFilter extends CFilter
{
	/**
	 * @var mixed a single primary CModel class name or array of classes model.
	 * The {@link getModel()} method will return a models of this classes.
	 * @see getModel
	 */
	protected $_models=array();
	/**
	 * @var string id of the {@link CActiveForm}
	 * @see CActiveForm::$id
	 * @see CActiveForm::$htmlOptions
	 */
	public $formId;
	/**
	 * @var string the name of the parameter indicating the request is an AJAX request.
	 * @see CActiveForm::$htmlOptions
	 */
	public $ajaxVar='ajax';

	/**
	 * Initializes the filter.
	 * @throws CException
	 */
	public function init()
	{
		if($this->formId===null)
			throw new CException(Yii::t('yiiext','The "{property}" property cannot be empty.',array(
				'{property}'=>'formId')));
	}
	/**
	 * Performs the pre-action filtering.
	 * @param CFilterChain the filter chain that the filter is on.
	 * @return boolean whether the filtering process should continue and the action
	 * should be executed.
	 */
	protected function preFilter($filterChain)
	{
		if(!empty($this->_models) && isset($_POST[$this->ajaxVar]) && $_POST[$this->ajaxVar]===$this->formId)
		{
			echo CActiveForm::validate($this->_models);
			Yii::app()->end();
		}
		return true;
	}
	/**
	 * Add models for validate.
	 * @param CModel
	 */
	public function setModels($models)
	{
		if(is_string($models))
			$this->_models[]=new $models;
		else if($models instanceof CModel)
			$this->_models[]=$models;
		else if(is_array($models))
			foreach($models as $model)
				$this->setModels($model);
	}
}
