ActiveForm Validation Filter
===================

При использовании [CActiveForm](http://www.yiiframework.com/doc/api/CActiveForm) с включенной AJAX валидацией,
в документации предлагают метод
~~~
[php]
protected function performAjaxValidation($model)
{
	if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
	{
		echo CActiveForm::validate($model);
		Yii::app()->end();
	}
}
~~~

Вместо этого, чтоб каждый раз не писать этот метод в контроллере,
можно воспользоваться этим фильтром для нужных действий.

Установка и настройка
--------------------------
~~~
[php]
public function filters()
{
	return array(
		array(
			'ext.yiiext.filters.activeFormValidation.EActiveFormValidationFilter[ +|- Action1, Action2, ...]',
			// Название или массив названий классов для проверяемой модели.
			// Метод EActiveFormValidationFilter::getModelsInternal() вернет массив моделей используя эти классы.
			'models'=>array(
				'ModelClassToBeValidate', // Передаем имя модели
				SecondModel:model()->findByPk(1), // Передаем объект модели
				array('modelClass'=>'ThirdModel','scenario'=>'update','isNewRecord'=>true), // Передаем имя модели а также сценарий
			),
			// Ид формы.
			'formId'=>'form',
			// Имя параметра используемого в AJAX-запросе.
			'ajaxVar'=>'ajax,
		),
	);
}
~~~
