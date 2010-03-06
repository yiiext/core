SetReturnUrl Filter
===================

Позволяет сохранять текущий URL в сессии для всех или выборочных действий
контроллера, чтобы затем к нему вернуться.


Установка и настройка
---------------------
Распаковать в папку `extensions` вашего приложения.

Настроить приложение (`config/main.php`):
~~~
[php]
return array(
    'import'=>array(
        // …
        'ext.yiiext.filters.setReturnUrl.ESetReturnUrlFilter',
    ),
    // …
);
~~~

Определить в контроллере метод `filters()`:
~~~
[php]
function filters() {
    return array(
        array(
            'ESetReturnUrlFilter',
            // Использовать для выбранных действий
            // 'ESetReturnUrlFilter + index',
        ),
    );
}
~~~

Использование
-------------
~~~
[php]
$this->redirect(Yii::app()->user->returnUrl);
~~~
