ActiveForm Validation Filter
===================

EActiveFormValidationFilter performs validation of active form.
To AJAX validation with {@link CActiveForm}, we can use this filter.

Installing and configuring your controller
--------------------------
~~~
[php]
public function filters()
{
	Yii::import('ext.yiiext.filters.activeFormValidation.EActiveFormValidationFilter');
	return array(
		array(
			'EActiveFormValidationFilter[ +|- Action1, Action2, ...]',
			// The single primary CModel class name or array of classes model.
			// The {@link getModel()} method will return a models of this classes.
			'modelClass'=>'ModelClassToBeValidate',
			// Id of the {@link CActiveForm}
			'formId'=>'form',
			// The name of the parameter indicating the request is an AJAX request.
			'ajaxVar'=>'ajax,
		),
	);
}
~~~
