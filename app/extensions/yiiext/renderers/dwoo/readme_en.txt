Dwoo view renderer
==================

This extension allows you to use [Dwoo](http://dwoo.org/) templates in Yii.

###Resources
* [SVN](http://code.google.com/p/yiiext/source/browse/trunk/app/extensions#extensions/yiiext/renderers/dwoo)
* [Dwoo](http://dwoo.org/)
* [Discuss](http://www.yiiframework.com/forum/index.php?/topic/4965-dwoo-view-renderer/)
* [Report a bug](http://code.google.com/p/yiiext/issues/list)

###Requirements
* Yii 1.0 or above

###Installation
* Extract the release file under `protected/extensions`.
* [Download](http://dwoo.org/download) and extract Dwoo (dwoo-x.x.x.tar\dwoo\) under `protected/vendors/Dwoo`.
* Add the following to your config file 'components' section:
~~~
[php]
'viewRenderer'=>array(
  'class'=>'ext.yiiext.renderers.dwoo.EDwooViewRenderer',
    'fileExtension' => '.tpl',
    //'pluginsDir' => 'application.dwooPlugins',
),
~~~

###Usage
* [Dwoo syntax](http://wiki.dwoo.org/index.php/Syntax).
* Current controller properties are accessible via {$this->pageTitle}.
* Yii properties are available as follows: {$Yii->theme->baseUrl}.
* Used memory is stored in {$MEMORY}, used time is in {$TIME}.