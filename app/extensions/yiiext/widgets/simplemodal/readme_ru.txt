SimpleModal widget
===============

Позволяет использовать [SimpleModal jQuery plugin](http://www.ericmmartin.com/projects/simplemodal/).

Использование
-------------
~~~
[php]
$this->widget('ext.yiiext.widgets.simplemodal.ESimpleModalWidget', array(

	// selector для элемента клик по которому будет открывать модальное окно
	'selector'=>'#open_modal_link',

	// HTML который будет отображаться в модальном окне
	'content'=>'<div class="mywindow">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>',

	// свойство плагина, подробнее на [оф. сайте](http://www.ericmmartin.com/projects/simplemodal/)
	'options'=>array(
		'close'=>true,
	),
));
~~~

Другой вариант использования с помощью beginWidget() и endWidget()
~~~
[php]
$this->beginWidget('ext.yiiext.widgets.simplemodal.ESimpleModalWidget', array(
	'selector'=>'#open_modal_link',
	'options'=>array(
		'close'=>true,
	),
));
<div class="mywindow">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>
$this->endWidget();
~~~
