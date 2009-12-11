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
        'taggable' => array(
            'class' => 'ext.Taggable.TaggableBehaviour',
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
$post->setTags('tag1, tag2, tag3');
$post->save();
~~~


### addTags($tags)
Add one or more tags to existing set.

~~~
[php]
$post->addTags('new1, new2');
$post->save();
~~~


### removeTags($tags)
Remove tags specified (if they do exist).

~~~
[php]
$post->removeTags('new1');
$post->save();
~~~

### removeAllTags()
Remove all tags from the model.

~~~
[php]
$post->removeAllTags();
$post->save();
~~~


### getTags()
Get comma separated model's tags string.

~~~
[php]
echo $post->getTags();
~~~

### getTagsArray()
Get array of model's tags.

~~~
[php]
$tags = $post->getTags();
foreach($tags as $tag){
  echo $tag;
}
~~~

### findAllByTags($tags, CDbCriteria $criteria = null)
Get all models having all tags specified and (optionally) criteria specified.

~~~
[php]
$posts = Post::model()->findAllByTags("mysql, yii");
~~~

### getCountByTags($tags, CDbCriteria $criteria = null)
Get count of models having all tags specified and (optionally) criteria specified.

~~~
[php]
$postsCount = Post::model()->getCountByTags("mysql, yii");
~~~
