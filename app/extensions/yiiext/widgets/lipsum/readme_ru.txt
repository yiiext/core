ELipsum генератор текста "Lorem Ipsum".
===========================

Использование в виде виджета:
-----
~~~
[php]
$this->widget('ext.yiiext.widgets.ELipsum',array(
	// Количество параграфов.
	// Если указать 0 (ноль) будет сгенерировано случайное число параграфов от 1 до 10.
	// По умолчанию '0'.
	'paragraphs'=>0,
	// Количество слов в каждом параграфе.
	// Если указать 0 (ноль) будет сгенерировано случайное число слов от 5 до 100.
	// По умолчанию '0'.
	'words'=>0,
	// Текст будет начинаться с фразы "Lorem ipsum dolor sit amet".
	// По умолчанию 'true'.
	'loremIpsumFirst'=>true,
	// В этот тег будет обернут текст каждого параграфа.
	// По умолчанию 'p'.
	'paragraphTag'=>'p',
));
~~~

Использование статичных методов:
-----
~~~
[php]
Yii::import('ext.yiiext.widgets.lipsum.ELipsum');
echo ELipsum::paragraphs(2);
echo ELipsum::words(100);
~~~

Использование в модели:
-----
~~~
public function rules()
{
	Yii::import('ext.yiiext.widgets.lipsum.ELipsum');
	return array(
		array('content','default','value'=>ELipsum::paragraphs(),'on'=>'insert'),
	);
}
~~~
