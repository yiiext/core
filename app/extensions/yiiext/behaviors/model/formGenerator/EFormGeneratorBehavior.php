<?php
/**
 * EFormGeneratorBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormGeneratorBehavior represents a form generator.
 *
 * This behavior helps to generate forms with {@link CFrom}, using information from the model.
 * The behavior sets form elements, after gets CModel:: getFormElements() of the model.
 * The method returns an array of elements, such as what is used in {@link CForm::setElements()}.
 * Model scenarios are also supported.
 *
 * Model example:
 * <pre>
 * class User extends CActiveRecord
 * {
 *     public function getFormElements()
 *     {
 *         return array(
 *             'login'=>array('type'=>'text','attributes'=>array('class'=>'login'),'on'=>'insert'),
 *             'password'=>array('type'=>'text','attributes'=>array('class'=>'password'),'on'=>'insert'),
 *             'email'=>array('type'=>'text','attributes'=>array('class'=>'email')),
 *             'desc'=>array('type'=>'textarea','attributes'=>array('class'=>'desc')),
 *         );
 *     }
 * }
 * </pre>
 *
 * Controller action example:
 * <pre>
 * class UsersController extends CController
 * {
 *     public function behaviors()
 *     {
 *         return array(
 *             'formGenerator'=>array(
 *                 'class'=>'ext.yiiext.behaviors.model.formGenerator.EFormGeneratorBehavior',
 *                 // form configuration
 *                 'config'=>array(
 *                     'attributes'=>array(
 *                         'class'=>'user_form',
 *                     ),
 *                 ),
 *             ),
 *         );
 *     }
 *     public function actionInsert()
 *     {
 *         $form=$this->formGenerator;
 *         $form->setModel(new User);
 *         // Add addition elements, example captcha validator
 *         $form->addModel(new CaptchaModel);
 *         $this->render('user_form');
 *     }
 *     public function actionUpdate()
 *     {
 *         $form=$this->formGenerator;
 *         $form->setModel(new User('update'));
 *         // Add addition elements, example captcha validator
 *         $form->addModel(new CaptchaModel);
 *         $this->render('user_form');
 *     }
 * }
 * </pre>
 *
 * And now in view render the form
 * <pre>
 * echo CHtml::openTag('div',array('id'=>'user_form','class'=>'user_form'));
 * // Because behavior is extends from CComponent you can use form instead of getForm().
 * echo $this->form;
 * echo CHtml::closeTag('div');
 * </pre>
 *
 * For more details about configuring form elements, please refer to {@link CFormInputElement}.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.behaviors.model.formGenerator
 */
class EFormGeneratorBehavior extends CBehavior
{
	/**
	 * @var string class name for CForm or his child.
	 */
	public $formClass='CForm';
	/**
	 * @var array initial configuration for form.
	 */
	public $config=array();
	/**
	 * @var CForm main object
	 */
	protected $_form;

	/**
	 * Create main form.
	 * @param array $config form configuration.
	 * @param CModel $model form model.
	 * @param CBaseController $owner instance for render CActiveForm widget
	 * @return EFormGeneratorBehavior
	 */
	public function createForm($config=array(),$model=null,$owner=null)
	{
		if($owner===null)
		{
			$owner=$this->getOwner();
			if(!($owner instanceof CBaseController))
				$owner=Yii::app()->getController();
		}

		$formClass=$this->formClass;
		$this->_form=new $formClass($config,null,$owner);
		if($model!==null)
			$this->setModel($model);

		return $this;
	}
	/**
	 * Get elements from model.
	 * @param CModel $model
	 * @return array
	 */
	public function getModelElements($model)
	{
		$elements=array();
		if($model instanceof CModel)
		{
			$scenario=$model->getScenario();
			foreach($model->getFormElements() as $name=>$element)
			{
				$on=array();
				if(isset($element['on']) && is_array($element['on']))
					$on=$element['on'];
				else if(isset($element['on']))
					$on=preg_split('/[\s,]+/',$element['on'],-1,PREG_SPLIT_NO_EMPTY);

				if(empty($on) || in_array($scenario,$on))
				{
					unset($element['on']);
					$elements[$name]=$element;
				}
			}
		}
		return $elements;
	}
	/**
	 * Add elements to form.
	 * @param array $elements
	 * @return EFormGeneratorBehavior
	 */
	public function setElements($elements)
	{
		$this->getForm()->setElements($elements);
		return $this;
	}
	/**
	 * Set model to form.
	 * @param CModel $model
	 * @return EFormGeneratorBehavior
	 */
	public function setModel($model)
	{
		$elements=$this->getModelElements($model);
		$this->getForm()->setModel($model);
		$this->setElements($elements);
		return $this;
	}
	/**
	 * Generate subform with model.
	 * @param CModel $model
	 * @return EFormGeneratorBehavior
	 */
	public function addModel($model)
	{
		if($this->getForm()->getModel()===null)
			$this->setModel($model);
		else if($elements=$this->getModelElements($model))
		{
			$elements=array(
				get_class($model)=>array(
					'type'=>'form',
					'model'=>$model,
					'elements'=>$elements,
				)
			);
			$this->setElements($elements);
		}
		return $this;
	}
	/**
	 * Add subform. Can be instance of CForm.
	 * @param CForm $form
	 * @return EFormGeneratorBehavior
	 */
	public function addFrom($form)
	{
		$this->setElements(array(
			$this->getForm()->getElements()->count()=>$form
		));
		return $this;
	}
	/**
	 * Get main form.
	 * @return CForm
	 */
	public function getForm()
	{
		if($this->_form===null)
		{
			$config=new CConfiguration(dirname(__FILE__).'/config.php');
			$config->mergeWith($this->config);
			$this->createForm($config->toArray());
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
}
