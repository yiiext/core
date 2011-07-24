PhpDoc
======

Позволяет читать phpDoc.

Использование
-------------
~~~
[php]
// В любом месте где нужно прочитать phpDoc
Yii::import('ext.yiiext.components.phpDoc.EPhpDoc');
$doc=new EPhpDoc(new ReflectionClass('CComponent'));
// или
$doc=new EPhpDoc(new ReflectionMethod('CComponent','raiseEvent'));
// выводим документацию
CVarDumper::dump($doc->toArray());
~~~
