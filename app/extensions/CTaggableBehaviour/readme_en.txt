TaggableBehaviour
=================
Allows active record model to manage tags.

Installation and configuration
------------------------------
Create a table where you want to store tags and cross-table to store tag-model connections.
You can use sample SQL from `schema.sql` file.

In your ActiveRecord model define `behaviors()` method:
~~~
[php]
function behaviors() {
    return array(
        'tags' => array(
            'class' => 'ext.СTaggableBehaviour.СTaggableBehaviour',
            // Table where tags are stored
            'tagTable' => 'Tag',
            // Cross-table that stores tag-model connections.
            // By default it's your_model_tableTag
            'tagBindingTable' => 'PostTag',
            // Foreign key in cross-table.
            // By default it's your_model_tableId
            'modelTableFk' => 'postId',
            // Caching component ID.
            // false by default.
            'CacheID' => 'cache',
            
            // Save nonexisting tags.
            // When false, throws exception when saving nonexisting tag.
            'createTagsAutomatically' => true,
        )
    );
}
~~~

Methods
-------
### setTags($tags)
Replace model tags with new tags set.

~~~
[php]
$post = new Post();
$post->setTags('tag1, tag2, tag3')->save();
~~~


### addTags($tags) or addTag($tags)
Add one or more tags to existing set.

~~~
[php]
$post->addTags('new1, new2')->save();
~~~


### removeTags($tags) or removeTag($tags)
Remove tags specified (if they do exist).

~~~
[php]
$post->removeTags('new1')->save();
~~~

### removeAllTags()
Remove all tags from the model.

~~~
[php]
$post->removeAllTags()->save();
~~~

### getTags()
Get array of model's tags.

~~~
[php]
$tags = $post->getTags();
foreach($tags as $tag){
  echo $tag;
}
~~~

### hasTag($tags) или hasTags($tags)
Returns true if all tags specified are assigned to current model and false otherwise.

~~~
[php]
$post = Post::model()->findByPk(1);
if($post->hasTags("yii, php")){
    //…
}
~~~

### getAllTags()
Get all possible tags for this model class.

~~~
[php]
$tags = Post::model()->getAllTags();
foreach($tags as $tag){
  echo $tag;
}
~~~

### getAllTagsWithModelsCount()
Get all possible tags with models count for each for this model class.
~~~
[php]
$tags = Post::model()->getAllTagsWithModelsCount();
foreach($tags as $tag){
  echo $tag['name']." (".$tag['count'].")";
}
~~~

### taggedWith($tags) или withTags($tags)
Limits AR query to records with all tags specified.

~~~
[php]
$posts = Post::model()->taggedWith('php, yii')->findAll();
$postCount = Post::model()->taggedWith('php, yii')->count();
~~~

Bonus features
--------------
You can print comma separated tags following way:
~~~
[php]
$post->addTags('new1, new2')->save();
echo $post->tags;
~~~