Form Generator
===================

Данное поведение помогает генерировать формы при помощи CFrom из моделей.
Нужно создать метод getFormElements(), который возвращает массив элементов для формы.
Можно использовать сценарии!

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
Модель готова.

Подключаем поведение к контроллеру и в действии генерируем форму
~~~
[php]
class UsersController extends Controller
{
	public function behaviors()
	{
		return array(
			'formGenerator'=>array(
				'class'=>'ext.yiiext.behaviors.model.formGenerator.EFormGeneratorBehavior',
				// Настройки формы
				'config'=>array(
					'attributes'=>array(
						'class'=>'feedback',
					),
					'activeForm'=>array(
						'id'=>'feedback-form',
					),
				),
			)
		);
	}
	public function actionInsert()
	{
		$form=$this->formGenerator;
		$form->setModel(new User('insert'));
		$form->addModel(new CaptchaModel);
		$this->render('user_form');
	}
	public function actionUpdate()
	{
		$form=$this->formGenerator;
		$form->setModel(new User('update'));
		$form->addModel(new CaptchaModel);
		$this->render('user_form');
	}
}
~~~

В представлении рендерим форму
~~~
[php]
echo CHtml::openTag('div',array('id'=>'user_form','class'=>'user_form'));
echo $this->form;
echo CHtml::closeTag('div');
~~~
