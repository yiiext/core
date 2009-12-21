Поведение для моделей, которым нужен статус, например статус постов.

Установка и настройка
---------------------
Добавить поле типа `varchar` в таблицу и модель. В примере, это поле `status`.

Сконфигурировать модель:
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
~~~

$post=Post::model()->findByPk(1);
echo $post->getStatus();
$post->setStatus('draft');
if ($post->save() === FASLE) {
    echo 'ошибки сохранения';
}

$post = Post::model()->findByPk(1);
$post->setStatus('draft')->saveStatus(); 