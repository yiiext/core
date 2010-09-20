<?php
/**
 * Default configuration of form. 
 */
return array(
	'activeForm'=>array(
		'enableAjaxValidation'=>true,
		'focus'=>'input[type="text"]:first',
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'validationDelay'=>1000,
		),
	),
	'buttons'=>array(
		'submit'=>array(
			'type'=>'submit',
			'label'=>Yii::t('yiiext','Submit'),
			'attributes'=>array('class'=>'button',),
		),
		'reset'=>array(
			'type'=>'reset',
			'label'=>Yii::t('yiiext','Reset'),
			'attributes'=>array('class'=>'button reset'),
		),
	),
);