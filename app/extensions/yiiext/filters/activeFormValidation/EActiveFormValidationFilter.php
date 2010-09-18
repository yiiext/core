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
 *             'modelClass'=>'ModelClassToBeValidate',
 *             'formId'=>'form',
 *             'ajaxVar'=>'ajax,
 *         ),
 *     );
 * }
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filters.activeFormValidation
 */
class EActiveFormValidationFilter extends CFilter
{
	/**
	 * @var mixed a single primary CModel class name or array of classes model.
	 * The {@link getModel()} method will return a models of this classes.
	 * @see getModel
	 */
	public $modelClass;
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
		if($this->modelClass===null)
			throw new CException(Yii::t('yiiext','The "{property}" property cannot be empty.',array(
				'{property}'=>'modelClass')));
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
		if(isset($_POST[$this->ajaxVar]) && $_POST[$this->ajaxVar]===$this->formId)
		{
			echo CActiveForm::validate($this->getModel());
			Yii::app()->end();
		}
		return true;
	}
	/**
	 * Returns the model to be validate.
	 * @return CModel the list of data items currently available in this data provider.
	 */
	protected function getModel()
	{
		$models=array();
		if(is_string($this->modelClass))
			$this->modelClass=array($this->modelClass);
		foreach($this->modelClass as $modelClass)
			$models[]=new $modelClass;
		return $models;
	}
}
