CSetReturnUrlFilter
===================

Позволяет сохранять текущий url в сессии для всех или выборочных действий
контроллера, чтобы затем к нему вернуться.


Установка и настройка
---------------------
Скопировать в папку `extensions` вашего приложения.
Определить в контроллере метод `filters()`:
~~~
[php]
function filters() {
    return array(
        array(
            'CSetReturnUrlFilter',
            // Использовать для выбранных действий
            // 'CSetReturnUrlFilter + index',
        ),
    );
}
        'ensureNull' => array(
            'class' => 'ext.EnsureNullBehavior.EnsureNullBehavior',
            // Использовать ли при обновлении записи
            // 'useOnUpdate' => false,
        )
    );
}
~~~
