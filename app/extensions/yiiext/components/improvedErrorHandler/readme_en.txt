Improved error handler
======================

A bit improved error handler. For now it can show trace call arguments, show
if method is called statically or dynamically and generally a bit more cleaner.

Installation
------------

Add to your `main.php` into `components` section:
~~~
[php]
'components'=>array(
	…
	'errorHandler' => array(
		'class' => 'ext.yiiext.components.improvedErrorHandler.EImprovedErrorHandler'
	),
),
~~~