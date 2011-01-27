ActiveForm Validation Filter
============================

При использовании [CActiveForm](http://www.yiiframework.com/doc/api/CActiveForm)
с включенной AJAX валидацией, в документации предлагают метод
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
---------------------
~~~
[php]
public function filters()
{
	return array(
		array(
			'ext.yiiext.filters.activeFormValidation.EActiveFormValidationFilter[ +|- Action1, Action2, ...]',
			// Название или массив названий классов для проверяемой модели.
			// Метод EActiveFormValidationFilter::getModel() вернет массив моделей используя эти классы.
			'models'=>array('ModelClassToBeValidate',new OtherModel('update')),
			// Ид формы.
			'formId'=>'form',
			// Имя параметра используемого в AJAX-запросе.
			'ajaxVar'=>'ajax,
		),
	);
}
~~~
