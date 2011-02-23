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
 * @version 0.4
 * @package yiiext.filters.activeFormValidation
 */
class EActiveFormValidationFilter extends CFilter
{
	/**
	 * @var mixed a single primary CModel class name or array of classes model.
	 * The {@link getModelsInternal()} method will return a models of this classes.
	 * @see getModelsInternal
	 */
	public $models=array();
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
	* @var string the action ID being filtered by this filter.
	*/
	private $_actionId;

	/**
	 * Performs the pre-action filtering.
	 * @param CFilterChain the filter chain that the filter is on.
	 * @return boolean whether the filtering process should continue and the action
	 * should be executed.
	 */
	protected function preFilter($filterChain)
	{
		$this->_actionId=$filterChain->action->getId();
		if($this->formId===null)
		{
			$this->formId=$this->_actionId.'-form';
		}
		if(isset($_POST[$this->ajaxVar])&&$_POST[$this->ajaxVar]===$this->formId)
		{
			$models=$this->getModelsInternal();
			if(!empty($models))
			{
				echo CActiveForm::validate($models);
				Yii::app()->end();
			}
		}
		return true;
	}
	protected function createModel($modelClass,$scenario=NULL,$isNewRecord=true)
	{
		if($scenario===null)
		{
			$scenario='ajax-'.$this->_actionId;
		}
		if($isNewRecord)
		{
			return new $modelClass($scenario);
		}
		$model=CActiveRecord::model($modelClass);
		$model->setScenario($scenario);
		return $model;
	}
	/**
	 * Add models for validate.
	 * @param CModel
	 */
	protected function getModelsInternal()
	{
		$models=array();
		if(!is_array($this->models))
		{
			$this->models=array($this->models);
		}
		foreach($this->models as $model)
		{
			if($model instanceof CModel)
			{
				$models[]=$model;
			}
			else if(is_string($model))
			{
				$models[]=$this->createModel($model);
			}
			else if(is_array($model))
			{
				isset($model['scenario'])||$model['scenario']=null;
				isset($model['isNewRecord'])||$model['isNewRecord']=true;
				$models[]=$this->createModel($model['modelClass'],$model['scenario'],$model['isNewRecord']);
			}
		}
		return $models;
	}
}
