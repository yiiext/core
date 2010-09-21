Iconized menu
=============

Allows you to create a menu with the corresponding favicon on the left of each item.

~~~
[php]
<?$this->widget('ext.yiiext.widgets.iconizedMenu.EIconizedMenu',array(
    // Uncomment to use Yandex instead of Google
    //'iconizerBaseUrl' => 'http://favicon.yandex.ru/favicon/',
    'items'=>array(
        array('label'=>'Yii Framework', 'url'=> 'http://yiiframework.com/'),
        array('label'=>'RMCreative', 'url'=> 'http://rmcreative.ru/'),
        array('label'=>'Twitter', 'url'=> 'http://twitter.com/'),
    ),
))?>
~~~