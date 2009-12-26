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
        'tags' => array(
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
$post->setTags('tag1, tag2, tag3')->save();
~~~


### addTags($tags) или addTag($tags)
Добавляет один или несколько тегов к уже существующим.

~~~
[php]
$post->addTags('new1, new2')->save();
~~~


### removeTags($tags) или removeTag($tags)
Удаляет указанные теги (если есть).

~~~
[php]
$post->removeTags('new1')->save();
~~~

### removeAllTags()
Удаляет все теги данной модели.

~~~
[php]
$post->removeAllTags()->save();
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

### hasTag($tags) или hasTags($tags)
Назаначены ли модели указанные теги.

~~~
[php]
$post = Post::model()->findByPk(1);
if($post->hasTags("yii, php")){
    //…
}
~~~

### getAllTags()
Отдаёт все имеющиеся для этого класса моделей теги.

~~~
[php]
$tags = Post::model()->getAllTags();
foreach($tags as $tag){
  echo $tag;
}
~~~

### getAllTagsWithModelsCount()
Отдаёт все имеющиеся для этого класса модели теги с количеством моделей для каждого.
~~~
[php]
$tags = Post::model()->getAllTagsWithModelsCount();
foreach($tags as $tag){
  echo $tag['name']." (".$tag['count'].")";
}
~~~

### taggedWith($tags) или withTags($tags)
Позволяет ограничить запрос AR записями с указанными тегами.

~~~
[php]
$posts = Post::model()->taggedWith('php, yii')->findAll();
$postCount = Post::model()->taggedWith('php, yii')->count();
~~~



Приятные бонусы
---------------
Теги, разделённые запятой можно распечатать следующим образом:
~~~
[php]
$post->addTags('new1, new2')->save();
echo $post->tags;
~~~