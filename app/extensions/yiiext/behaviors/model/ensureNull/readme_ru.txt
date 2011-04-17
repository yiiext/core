Ensure NULL behavior
====================

Сохраняет пустые значения атрибута AR как `NULL`, если по умоланию для атрибута
они равны `NULL`. Полезен для предотвращения появления пустых значений в БД и
использования вместо них `NULL`.


Установка и настройка
---------------------
Скопировать в папку `extensions` вашего приложения.
Определить в модели ActiveRecord метод `behaviors()`:
~~~
[php]
function behaviors() {
    return array(
        'ensureNull' => array(
            'class' => 'ext.yiiext.behaviors.model.ensureNull.EEnsureNullBehavior',
            // Использовать ли при обновлении записи
            // 'useOnUpdate' => false,
        )
    );
}
~~~
