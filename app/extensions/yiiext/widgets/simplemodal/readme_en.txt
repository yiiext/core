SimpleModal widget
===============

Add [SimpleModal jQuery plugin](http://www.ericmmartin.com/projects/simplemodal/) widget.

Usage
-----
~~~
[php]
$this->widget('ext.yiiext.widgets.simplemodal.ESimpleModalWidget', array(

	// trigger element selector
	'selector'=>'#open_modal_link',

	// the dialog HTML content
	'content'=>'<div class="mywindow">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>',

	// extension options. For more details read [documentation](http://www.ericmmartin.com/projects/simplemodal/)
	'options'=>array(
		'close'=>true,
	),
));
~~~

Another variant to use with beginWidget() и endWidget() methods
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
