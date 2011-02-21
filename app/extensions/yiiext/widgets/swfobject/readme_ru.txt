ESwfobjectWidget встраивает Adobe Flash Player контент с помощью SWFObject 2.
===========================

Описание SWFObject читайте на http://code.google.com/p/swfobject/

Использование
-----
в представлении:
~~~
[php]
$this->beginWidget('ext.yiiext.widgets.swfobject.ESwfObjectWidget',array(
	// Тег для контейнера. По умолчанию 'div.
	'tag'=>'div',
	// Настройки контейнера.
	'htmlOptions'=>array(),
	// Ссылка на флеш файл.
	'swfUrl'=>'/files/movie.swf',
	// Ширина плеера.
	'width'=>100,
	// Высота плеера.
	'height'=>50,
	// Требования к версии флеш плеера.
	'version'=>'8',
	// Массив flashvars.
	'flashvars'=>array(),
	// Массив params.
	'params'=>array(),
	// Массив attributes.
	'attributes'=>array(),
	// Callback-функция.
	'callbackFn'=>false,
	// Позиция скрипта.
	'scriptPosition'=>CClientScript::POS_READY
));
Текст который будет показан пользователю у которого не установлен флеш-плеер.
$this->endWidget();
~~~
