Image Column для CGridView
==========================

Позволяет отображать изображения
в ячейках [CGridView](http://www.yiiframework.com/doc/api/CGridView).

Установка
---------

Распаковать в `protected/extensions`.

В `config/main.php` добавить:
~~~
[php]
'import'=>array(
	'ext.yiiext.zii.widgets.grid.imageColumn.EImageColumn',
),
~~~

Пример использования
--------------------
~~~
[php]
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
	'columns'=>array(
		'title:html',
		'description:html',
		array(
			'class' => 'EImageColumn',
			// См. ниже.
			'imagePathExpression' => '/images/.$data->imagePath',
			// Текст, отображаемый, если в ячейке пусто.
			// Можно не задавать.
			'emptyText' => '—',
			// Настройки тега img
			'imageOptions' => array(
				'alt' => 'no',
				'width' => 120,
				'height' => 120,
			),
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
));
~~~

`imagePathExpression` — выражение PHP, которое вычислятся для каждой ячейки и
используется как путь к изображению. В данном выражении можно использовать:

- `$row` — порядковый номер строки, начинающийся с нуля.
- `$data` — модель, соответствующая текущей строке.
- `$this` — экземпляр `EImageColumn`.  
