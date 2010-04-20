Image Column for CGridView
==========================

Can be used to display image in
[CGridView](http://www.yiiframework.com/doc/api/CGridView) cell.

Installation
------------

Extract to `protected/extensions`.

Add following code to `config/main.php`:
~~~
[php]
'import'=>array(
    'ext.yiiext.zii.widgets.grid.imageColumn.EImageColumn',
),
~~~

Usage example
-------------
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
            // see below.
            'imagePathExpression' => '/images/.$data->imagePath',
            // Text used when cell is empty.
            // Optional.
            'emptyText' => '—',
            // HTML options for image tag. Optional.
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

`imagePathExpression` — a PHP expression that is evaluated for every data cell
and whose result is used as the path to image. Following variables are available:

- `$row` — the row number (zero-based).
- `$data` — the data model for the row.
- `$this` — instance of `EImageColumn`.  
