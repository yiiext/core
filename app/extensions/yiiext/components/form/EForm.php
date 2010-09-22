<?php
/**
 * EForm class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EForm represents a form object that contains form input specifications.
 *
 * This is extended version of CForm. Altered objects that store the elements collection.
 * We can add as element the model that return elements via method CModel::getFormElements().
 * Model scenarios are also supported.
 * 
 * For more details about configuring form elements, please refer to {@link CFormInputElement}.
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
 * Now you can easy generate from, example two action from UsersController:
 * <pre>
 * public function actionInsert()
 * {
 *     // Create form
 *     $form=new EForm(array(
 *         'activeForm'=>array(
 *             'id'=>'user_form',
 *             'enableAjaxValidation'=>true,
 *             'focus'=>'input[type="text"]:first',
 *             'clientOptions'=>array(
 *                 'validateOnSubmit'=>true,
 *                 'validationDelay'=>1000,
 *     )));
 *     // Add elements to form
 *     $form->elements=array(
 *         new User('insert'),
 *         new CaptchaModel,
 *     );
 *     // Validate
 *     if($form->submitted() && $form->validate())
 *     {
 *         // save models...
 *     }
 *    // Render view with form
 *     $this->render('user_form',array(
 *         'form'=>$form,
 *     ));
 * }
 * public function actionUpdate()
 * {
 *     // Create form
 *     $form=new EForm(array(
 *         'activeForm'=>array(
 *             'id'=>'user_form',
 *             'enableAjaxValidation'=>true,
 *             'focus'=>'input[type="text"]:first',
 *             'clientOptions'=>array(
 *                 'validateOnSubmit'=>true,
 *                 'validationDelay'=>1000,
 *     )));
 *     // Add elements to form
 *     $form->elements=array(
 *         new User('update'),
 *         new CaptchaModel,
 *     );
 *     // Validate
 *     if($form->submitted() && $form->validate())
 *     {
 *         // save models...
 *     }
 *    // Render view with form
 *     $this->render('user_form',array(
 *         'form'=>$form,
 *     ));
 * }
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.form
 */
require_once(dirname(__FILE__).'/EFormElementCollection.php');
class EForm extends CForm
{
	private $_elements;
	private $_buttons;

	public function getElements()
	{
		if($this->_elements===null)
			$this->_elements=new EFormElementCollection($this,false);
		return $this->_elements;
	}
	public function getButtons()
	{
		if($this->_buttons===null)
			$this->_buttons=new EFormElementCollection($this,true);
		return $this->_buttons;
	}
}
