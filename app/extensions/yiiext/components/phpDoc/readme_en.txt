PhpDoc
===============

Read phpDoc.

Usage
-------------
~~~
[php]
// Where yiu need read phpDoc
Yii::import('ext.yiiext.components.phpDoc.EPhpDoc');
$doc=new EPhpDoc(new ReflectionClass('CComponent'));
// or
$doc=new EPhpDoc(new ReflectionMethod('CComponent','raiseEvent'));
// and show it
CVarDumper::dump($doc->toArray());
~~~
