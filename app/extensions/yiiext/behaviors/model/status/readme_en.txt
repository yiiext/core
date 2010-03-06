Status Behavior
===============

Can be used with models to add functions to manage model status.

Installing and configuring
--------------------------
Add field to model and DB table. Field `status` is used as example.
Field type depends on behavior configuration.

Configure model:
~~~
[php]
class Post extends CActiveRecord {
    public function behaviors() {
        return array(
            'statuses' => array(
                'class' => 'ext.yiiext.behaviors.model.status.EStatusBehavior',
                // Field used for status
                'statusField' => 'status',
                // Allowed statuses. Default is array('draft', 'published', 'archived').
                // One can pass an array where key is DB field name, value is
                // what user will see. Also you can use localized status messages.
                // 'statuses' => array('draft', 'published', 'archived'),
                // Status group. Default values is "default".
                // Used for Yii::t('default', ...).
                // 'statusGroup' => 'default',
            ),
        );
    }
}
~~~

Examples
--------

### Configuring models
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
                // Using a group of statuses to separate translations.
                // Translations are stored in /extensionDirectory/messages/language/group.php
                'statusGroup' => 'books',
            ),
        );
    }
}
~~~

### Using statuses
~~~
[php]
$post=Post::model()->findByPk(1);
// Getting current status
echo $post->getStatus();
// Changing status
$post->setStatus('draft');
// Saving model
if ($post->save() === FASLE) {
    echo 'Error!';
}

$post = Post::model()->findByPk(1);
// Changing status field only
$post->setStatus('draft')->saveStatus();
~~~
