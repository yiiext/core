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
                // Передается массив, ключ каждого элемента массива для сохранения в БД, значение выводится пользователю,
                // 'statuses' => array('draft', 'published', 'archived'),
                // 'statuses' => array('d' => 'draft', 'p' => 'published', 'a' => 'archived'),
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
            ),
        );
    }
}
~~~

### Использование
~~~
[php]
$post=Post::model()->findByPk(1);
// Выводим статус
echo $post->getStatus();
// Изменяем статус
$post->setStatus('draft');
// Сохраняем модель
if ($post->save() === FASLE) {
    echo 'ошибки сохранения';
}

$post = Post::model()->findByPk(1);
// Изменяем статус cохраняем только поле status
$post->setStatus('draft')->saveStatus();
~~~
