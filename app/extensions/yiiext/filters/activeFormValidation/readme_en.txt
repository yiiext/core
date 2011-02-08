ActiveForm Validation Filter
===================

EActiveFormValidationFilter performs validation of active form.
To AJAX validation with [CActiveForm](http://www.yiiframework.com/doc/api/CActiveForm), we can use this filter.

Installing and configuring your controller
--------------------------
~~~
[php]
public function filters()
{
	return array(
		array(
			'ext.yiiext.filters.activeFormValidation.EActiveFormValidationFilter[ +|- Action1, Action2, ...]',
			// The single primary CModel class name or array of classes model.
			// The EActiveFormValidationFilter::getModelsInternal() method will return a models of this classes.
			'models'=>array(
				'ModelClassToBeValidate', // Set model name
				SecondModel:model()->findByPk(1), // Set model instance
				array('modelClass'=>'ThirdModel','scenario'=>'update','isNewRecord'=>true), // Set model name and scenario
			),
			// Id of the form
			'formId'=>'form',
			// The name of the parameter indicating the request is an AJAX request.
			'ajaxVar'=>'ajax,
		),
	);
}
~~~
