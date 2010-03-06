Шаблонизатор Quicky для Yii
===========================

Данное расширение позволяет использовать [Quicky](http://code.google.com/p/quicky/)
в шаблонах Yii.

###Полезные ссылки
* [SVN](http://code.google.com/p/yiiext/source/browse/trunk/app/extensions#extensions/yiiext/renderers/quicky)
* [Quicky](http://code.google.com/p/quicky/)
* [Обсуждение](http://yiiframework.ru/forum/viewtopic.php?f=9&t=240)
* [Соощить об ошибке](http://code.google.com/p/yiiext/issues/list)

###Требования
* Yii 1.0 и выше

###Установка
* Распаковать в `protected/extensions`.
* [Скачать](http://code.google.com/p/quicky/) и распаковать в `protected/vendors/Quicky`.
* Добавить в конфигурацю в секцию 'components':
~~~
[php]
'viewRenderer'=>array(
  'class'=>'ext.yiiext.renderers.quicky.EQuickyViewRenderer',
    'fileExtension' => '.tpl',
    //'pluginsDir' => 'application.quickyPlugins',
    //'configDir' => 'application.quickyConfig',
),
~~~

###Использование
* Quicky синтаксис похож на [Smarty](http://www.smarty.net/docs.php).
* Свойства текущего контроллера доступны как {$this->pageTitle}.
* Свойства Yii доступны как {$Yii->theme->baseUrl}.
* Использованную память можно вывести как {$MEMORY}, затраченное время как {$TIME}.
