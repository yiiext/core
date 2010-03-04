CQuickyViewRenderer
===================

This extension allows you to use [Quicky](http://code.google.com/p/quicky/) templates in Yii.

###Resources
* [SVN](http://code.google.com/p/yiiext/source/browse/#svn/trunk/app/extensions/CQuickyViewRenderer)
* [Quicky](http://code.google.com/p/quicky/)
* [Discuss](http://www.yiiframework.com/forum/index.php?/topic/4924-quicky-view-renderer/)
* [Report a bug](http://code.google.com/p/yiiext/issues/list)

###Requirements
* Yii 1.0 or above

###Installation
* Extract the release file under `protected/extensions`.
* [Download](http://code.google.com/p/quicky/) and extract Quicky under `protected/vendors/Quicky`.
* Add the following to your config file 'components' section:
~~~
[php]
'viewRenderer'=>array(
  'class'=>'application.extensions.CQuickyViewRenderer.CQuickyViewRenderer',
    'fileExtension' => '.tpl',
    //'pluginsDir' => 'application.quickyPlugins',
    //'configDir' => 'application.quickyConfig',
),
~~~

###Usage
* Quicky syntax is pretty much like [Smarty](http://www.smarty.net/docs.php) one.
* Current controller properties are accessible via {$this->pageTitle}.
