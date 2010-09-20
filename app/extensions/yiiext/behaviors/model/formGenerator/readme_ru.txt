Form Generator
===================

Данное поведение помогает генерировать формы при помощи [CForm](http://www.yiiframework.com/doc/api/CForm), используя информацию из модели.
Поведение получает элементы выполнив метод CModel::getFormElements(), в указанной модели.
Метод возвращает массив элементов, который используется в [CForm::setElements()](http://www.yiiframework.com/doc/api/CForm#setElements-detail).
Поддерживаются сценарии модели.

Пример модели:
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

Подключаем поведение к контроллеру и в действии генерируем форму
~~~
[php]
class UsersController extends CController
{
	public function behaviors()
	{
		return array(
			'formGenerator'=>array(
				'class'=>'ext.yiiext.behaviors.model.formGenerator.EFormGeneratorBehavior',
				// Настройки формы
				'config'=>array(
					'attributes'=>array(
						'class'=>'user_form',
				),
			)
		);
	}
	public function actionInsert()
	{
		$form=$this->formGenerator;
		$form->setModel(new User);
		// Добавляем дополнительную форму, например каптчу,
		$form->addModel(new CaptchaModel);
		$this->render('user_form');
	}
	public function actionUpdate()
	{
		$form=$this->formGenerator;
		$form->setModel(new User('update'));
		// Добавляем дополнительную форму, например каптчу,
		$form->addModel(new CaptchaModel);
		$this->render('user_form');
	}
}
~~~

Теперь все готово. Осталось только в представлении нарисовать форму
~~~
[php]
echo CHtml::openTag('div',array('id'=>'user_form','class'=>'user_form'));
echo $this->form;
echo CHtml::closeTag('div');
~~~

Для большей информации о настройки элементов, пожалуйста прочитайте [CFormInputElement](http://www.yiiframework.com/doc/api/CFormInputElement).
