ESwfobjectWidget embedding Adobe Flash Player content using SWFObject 2.
===========================

More details about SWFObject see on http://code.google.com/p/swfobject/

Usage
-----
in a view:
~~~
[php]
$this->beginWidget('ext.yiiext.widgets.swfobject.ESwfObjectWidget',array(
	// The tag name. It used flash container. Defaults to 'div.
	'tag'=>'div',
	// Html options for container.
	'htmlOptions'=>array(),
	// Flash url.
	'swfUrl'=>'/files/movie.swf',
	// Flash width.
	'width'=>100,
	// Flash height.
	'height'=>50,
	// Flash Player version required.
	'version'=>'8',
	// Array of flash vars.
	'flashvars'=>array(),
	// Array of params.
	'params'=>array(),
	// Array of attributes.
	'attributes'=>array(),
	// Callback function.
	'callbackFn'=>false,
	// Script position.
	'scriptPosition'=>CClientScript::POS_READY
));
Alternative text will be show if flash not installed.
$this->endWidget();
~~~
