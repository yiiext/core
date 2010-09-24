Обёртка для плагина jQuery Fancybox
====================================

Установка и настройка
---------------------
Импортируем наши классы, например через конфиг приложения (`config/main.php`):
~~~
[php]
return array(
    'import'=>array(
        // …
        'ext.yiiext.widgets.fancybox.*',
    ),
    // …
);
~~~

Используем в представлении `views/photos.php`:
~~~
[php]
$this->widget('EFancyboxWidget', array(

	// Селектор фото
	//'selector' => 'a[href$=.jpg],a[href$=.png],a[href$=.gif]',

	// Включаем колесико мыши для перематывания картинок в пределах группы
	// 'enableMouseWheel' => FALSE,

	// Группа картинок для данной галерии, для отключения указать 'group' => NULL.
	// 'group' => 'gallery',

	// Дальше свойство самого плагина fancybox. (@see http://fancybox.net/api)
	// 'padding' => 10,
	// 'margin' => 20,
	// /* ... */
	// 'enableEscapeButton' => TRUE,

	// Свойство плагина, позволяющие задать JavaScript код, вводим с префиксом `js:`
	// 'onComplete' => 'js:function() {$("#fancybox-wrap").hover(function() {$("#fancybox-title").show();}, function() {$("#fancybox-title").hide();});}',
));
~~~

Приятное дополнение, чтоб вывести в представлении картинку с ссылкой, можно воспользоватся хелпером
EFancyboxWidget::image($imageSrc, $imageAlt, $imageHtmlOptions, $linkHtmlOptions);
~~~
[php]
echo EFancyboxWidget::image('/images/photo1.jpg', 'Example 1', array(), array('class' => 'fancy_link'));
echo EFancyboxWidget::image('/images/photo2.jpg', 'Example 2', array(), array('class' => 'fancy_link'));
$this->widget('EFancyboxWidget', array(
	'selector' => '.fancy_link', 
));
~~~
