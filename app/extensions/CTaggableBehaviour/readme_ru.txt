TaggableBehaviour
=================
Позволяет модели работать с тегами.

Установка и настройка
---------------------
Создать таблицу для хранения тегов и кросс-таблицу для связи тегов с моделью.
Для конфигурации ниже можно воспользоваться SQL из файла `schema.sql`.

Определить в модели ActiveRecord метод `behaviors()`:
~~~
[php]
function behaviors() {
    return array(
        'taggable' => array(
            'class' => 'ext.СTaggableBehaviour.СTaggableBehaviour',
            // Имя таблицы для хранения тегов 
            'tagTable' => 'Tag',
            // Имя кросс-таблицы, связывающей тег с моделью.
            // По умолчанию выставляется как Имя_таблицы_моделиTag
            'tagBindingTable' => 'PostTag',
            // Имя внешнего ключа модели в кроcc-таблице.
            // По умолчанию равно имя_таблицы_моделиId 
            'modelTableFk' => 'postId',
            // ID компонента, реализующего кеширование.
            // По умолчанию ID равен false. 
            'CacheID' => 'cache',

            // Создавать несуществующие теги автоматически.
            // При значении false сохранение выкидывает исключение если добавляемый тег не существует.
            'createTagsAutomatically' => true,
        )
    );
}
~~~

Методы
------
### setTags($tags)
Задаёт новые теги для модели затирая старые.

~~~
[php]
$post = new Post();
$post->setTags('tag1, tag2, tag3');
$post->save();
~~~


### addTags($tags)
Добавляет один или несколько тегов к уже существующим.

~~~
[php]
$post->addTags('new1, new2');
$post->save();
~~~


### removeTags($tags)
Удаляет указанные теги (если есть).

~~~
[php]
$post->removeTags('new1');
$post->save();
~~~

### removeAllTags()
Удаляет все теги данной модели.

~~~
[php]
$post->removeAllTags();
$post->save();
~~~

### getTags()
Отдаёт массив тегов.

~~~
[php]
$tags = $post->getTags();
foreach($tags as $tag){
  echo $tag;
}
~~~

### findAllByTags($tags, CDbCriteria $criteria = null)
Отдаёт все модели с такими тегами и (опционально) критерием.

~~~
[php]
$posts = Post::model()->findAllByTags("mysql, yii");
~~~

### getCountByTags($tags, CDbCriteria $criteria = null)
Отдаёт количество моделей с такими тегами и (опционально) критерием.

~~~
[php]
$postsCount = Post::model()->getCountByTags("mysql, yii");
~~~
