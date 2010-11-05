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
			// The EActiveFormValidationFilter::getModel() method will return a models of this classes.
			'models'=>array('ModelClassToBeValidate',new OtherModel('update')),
			// Id of the form
			'formId'=>'form',
			// The name of the parameter indicating the request is an AJAX request.
			'ajaxVar'=>'ajax,
		),
	);
}
~~~
