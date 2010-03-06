Шаблонизатор Dwoo для Yii
=========================

Данное расширение позволяет использовать [Dwoo](http://dwoo.org/) в шаблонах Yii.

###Полезные ссылки
* [SVN](http://code.google.com/p/yiiext/source/browse/trunk/app/extensions#extensions/yiiext/renderers/dwoo)
* [Dwoo](http://dwoo.org/)
* [Обсуждение](http://yiiframework.ru/forum/viewtopic.php?f=9&t=245)
* [Соощить об ошибке](http://code.google.com/p/yiiext/issues/list)

###Требования
* Yii 1.0 и выше

###Установка
* Распаковать в `protected/extensions`.
* [Скачать](http://dwoo.org/download) и распаковать `dwoo-x.x.x.tar\dwoo\` в `protected/vendors/Dwoo`.
* Добавить в конфигурацю в секцию 'components':
~~~
[php]
'viewRenderer'=>array(
  'class'=>'ext.yiiext.renderers.dwoo.EDwooViewRenderer',
    'fileExtension' => '.tpl',
    //'pluginsDir' => 'application.dwooPlugins',
),
~~~

###Использование
* [Синтаксис Dwoo](http://wiki.dwoo.org/index.php/Syntax).
* Свойства текущего контроллера доступны как {$this->pageTitle}.
* Свойства Yii доступны как {$Yii->theme->baseUrl}.
* Использованную память можно вывести как {$MEMORY}, затраченное время как {$TIME}.