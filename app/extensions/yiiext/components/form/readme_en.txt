EForm
=====

EForm represents a form object that contains form input specifications.

This is extended version of CForm. Altered objects that store the elements collection.
We can add as element the model that return elements via method CModel::getFormElements().
Model scenarios are also supported.

For more details about configuring form elements, please refer to {@link CFormInputElement}.

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

Now you can easy generate from, example two action from UsersController:
~~~
[php]
public function actionInsert()
{
    // Create form
    $form=new EForm(array(
        'activeForm'=>array(
            'id'=>'user_form',
            'enableAjaxValidation'=>true,
            'focus'=>'input[type="text"]:first',
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validationDelay'=>1000,
    )));
    // Add elements to form
    $form->elements=array(
        new User('insert'),
        new CaptchaModel,
    );
    // Validate
    if($form->submitted() && $form->validate())
    {
        // save models...
    }
   // Render view with form
    $this->render('user_form',array(
        'form'=>$form,
    ));
}
public function actionUpdate()
{
    // Create form
    $form=new EForm(array(
        'activeForm'=>array(
            'id'=>'user_form',
            'enableAjaxValidation'=>true,
            'focus'=>'input[type="text"]:first',
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validationDelay'=>1000,
    )));
    // Add elements to form
    $form->elements=array(
        new User('update'),
        new CaptchaModel,
    );
    // Validate
    if($form->submitted() && $form->validate())
    {
        // save models...
    }
   // Render view with form
    $this->render('user_form',array(
        'form'=>$form,
    ));
}
~~~
