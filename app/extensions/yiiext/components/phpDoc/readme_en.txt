PhpDoc
======

Allows reading phpDoc.

Usage
-----
~~~
[php]
// First we need to import extension class
Yii::import('ext.yiiext.components.phpDoc.EPhpDoc');
$doc=new EPhpDoc(new ReflectionClass('CComponent'));
// or
$doc=new EPhpDoc(new ReflectionMethod('CComponent','raiseEvent'));
// finally showing what we've got
CVarDumper::dump($doc->toArray());
~~~
