Обёртка для плагина jQuery Fancybox
====================================

Используем:
----
в представлении `views/photos.php`:
~~~
[php]
$this->widget('ext.yiiext.widgets.fancybox.EFancyboxWidget',array(
	// Селектор фото
	//'selector'=>'a[href$=\'.jpg\'],a[href$=\'.png\'],a[href$=\'.gif\']',
	// Включаем колесико мыши для перематывания картинок в пределах группы.
	// По умолчанию выключено.
	// 'enableMouseWheel'=>false,
	// [Свойства fancybox](http://fancybox.net/api/).
	'options'=>array(
		// 'padding'=>10,
		// 'margin'=>20,
		// 'enableEscapeButton'=>true,
		// 'onComplete'=>'js:function() {$("#fancybox-wrap").hover(function() {$("#fancybox-title").show();}, function() {$("#fancybox-title").hide();});}',
	),
));
~~~
