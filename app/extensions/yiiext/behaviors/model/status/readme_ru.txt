Status Behavior
===============

Поведение для моделей, которым нужен статус, например статус постов.

Установка и настройка
---------------------
Добавить поле в таблицу и модель. В примере, это поле `status`.
Тип поля зависит от настроек поведения.

Сконфигурировать модель:
~~~
[php]
class Post extends CActiveRecord {
    public function behaviors() {
        return array(
            'statuses' => array(
                'class' => 'ext.yiiext.behaviors.model.status.EStatusBehavior',
                // Поле зарезервированное для статуса
                'statusField' => 'status',
                // Разрешенные статусы, по умолчнию array('draft', 'published', 'archived').
                // Передается массив, ключ каждого элемента массива для сохранения в БД,
                // значение выводится пользователю, при наличии локализации выводится переведенное значение
                // 'statuses' => array('draft', 'published', 'archived'),
                // Группа статусов, по умолчанию default.
                // Используется при локализации для функции Yii::t('default', ...).
                // 'statusGroup' => 'default',
            ),
        );
    }
}
~~~

Примеры
-------

### Подключение
~~~
[php]
class Post extends CActiveRecord {
    public function behaviors() {
        return array(
            'statuses' => array(
                'class' => 'ext.CStatusBehavior.CStatusBehavior',
                'statusField' => 'status',
            ),
        );
    }
}

class Book extends CActiveRecord {
    public function behaviors() {
        return array(
            'statuses' => array(
                'class' => 'ext.CStatusBehavior.CStatusBehavior',
                'statusField' => 'status',
                'statuses' => array(
                  'new' => 'new',
                  'reserved' => 'reserved',
                  'sale' => 'sale',
                ),
                // Используем группу статусов чтоб отделить переводы от других
                // переводы статусов хранятся в \\папкаСРасширением\messages\язык\имяГруппы.php
                'statusGroup' => 'books',
            ),
        );
    }
}
~~~

### Использование
~~~
[php]
// Выбираем запись
$post=Post::model()->findByPk(1);
// Выводим статус
echo $post->getStatus();
// Изменяем статус
$post->setStatus('draft');
// Сохраняем модель
if ($post->save() === FASLE) {
    echo 'ошибки сохранения';
}

// Выбираем запись
$post = Post::model()->findByPk(1);
// Изменяем статус cохраняем только поле status
$post->setStatus('draft')->saveStatus();
~~~
