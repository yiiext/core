Iconized menu
=============

Позволяет создать меню, где слева от каждой ссылки будет показываться значёк favicon.

~~~
[php]
<?$this->widget('ext.yiiext.widgets.iconizedMenu.EIconizedMenu',array(
    // Отключает использование спрайтов Яндекса и позволяет задать свой iconizerBaseUrl
    // 'useSprites' => false,
    'items'=>array(
        array('label'=>'Yii Framework', 'url'=> 'http://yiiframework.com/'),
        array('label'=>'RMCreative', 'url'=> 'http://rmcreative.ru/'),
        array('label'=>'Twitter', 'url'=> 'http://twitter.com/'),
    ),
))?>
~~~