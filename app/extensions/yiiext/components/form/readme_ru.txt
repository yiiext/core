EForm
=====

EForm генератор форм.

Это расширенная версия CForm. Добавлена возможность добавлять четвертый тип элементов.
А именно модели со специальным методом CModel::getFormElements().
Это метод должен возвращать массив элементов для формы.
Сохранена возможность работы сценариев.

Для деталей по элементам, прочитайте {@link CFormInputElement}.

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

Теперь легко создадим форму, пример двух действий из UsersController:
~~~
[php]
public function actionInsert()
{
    // Создаем форму
    $form=new EForm(array(
        'activeForm'=>array(
            'id'=>'user_form',
            'enableAjaxValidation'=>true,
            'focus'=>'input[type="text"]:first',
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validationDelay'=>1000,
    )));
    // Добавим елементы из моделей
    $form->elements=array(
        new User('insert'),
        new CaptchaModel,
    );
    // Проверяем переданные данные
    if($form->submitted() && $form->validate())
    {
        // сохраняем модели...
    }
   // показываем представление с формой
    $this->render('user_form',array(
        'form'=>$form,
    ));
}
public function actionUpdate()
{
    // Создаем форму
    $form=new EForm(array(
        'activeForm'=>array(
            'id'=>'user_form',
            'enableAjaxValidation'=>true,
            'focus'=>'input[type="text"]:first',
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validationDelay'=>1000,
    )));
    // Добавим елементы из моделей
    $form->elements=array(
        new User('update'),
        new CaptchaModel,
    );
    // Проверяем переданные данные
    if($form->submitted() && $form->validate())
    {
        // сохраняем модели...
    }
   // показываем представление с формой
    $this->render('user_form',array(
        'form'=>$form,
    ));
}
~~~
