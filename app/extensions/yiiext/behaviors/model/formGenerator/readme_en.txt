Form Generator
===================

This behavior helps to generate forms with [CForm](http://www.yiiframework.com/doc/api/CForm), using information from the model.
The behavior sets form elements, after gets CModel:: getFormElements() of the model.
The method returns an array of elements, such as what is used in [CForm::setElements()](http://www.yiiframework.com/doc/api/CForm#setElements-detail).
Model scenarios are also supported.

Model example:
~~~
[php]
class User extends CActiveRecord
{
    public function getFormElements()
    {
        return array(
            'login'=>array('type'=>'text','attributes'=>array('class'=>'login'),'on'=>'insert'),
            'password'=>array('type'=>'text','attributes'=>array('class'=>'password'),'on'=>'insert'),
            'email'=>array('type'=>'text','attributes'=>array('class'=>'email')),
            'desc'=>array('type'=>'textarea','attributes'=>array('class'=>'desc')),
        );
    }
}
~~~

Controller action example:
~~~
[php]
class UsersController extends CController
{
    public function behaviors()
    {
        return array(
            'formGenerator'=>array(
                'class'=>'ext.yiiext.behaviors.model.formGenerator.EFormGeneratorBehavior',
                // form configuration
                'config'=>array(
                    'attributes'=>array(
                        'class'=>'user_form',
                    ),
                ),
            ),
        );
    }
    public function actionInsert()
    {
        $form=$this->formGenerator;
        $form->setModel(new User);
        // Add addition elements, example captcha validator
        $form->addModel(new CaptchaModel);
        $this->render('user_form');
    }
    public function actionUpdate()
    {
        $form=$this->formGenerator;
        $form->setModel(new User('update'));
        // Add addition elements, example captcha validator
        $form->addModel(new CaptchaModel);
        $this->render('user_form');
    }
}
~~~

And now in view render the form
~~~
[php]
echo CHtml::openTag('div',array('id'=>'user_form','class'=>'user_form'));
// Because behavior is extends from CComponent you can use form instead of getForm().
echo $this->form;
echo CHtml::closeTag('div');
~~~

For more details about configuring form elements, please refer to [CFormInputElement](http://www.yiiframework.com/doc/api/CFormInputElement).
