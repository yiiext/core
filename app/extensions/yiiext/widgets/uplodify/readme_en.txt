Widget for jQuery Uploadify
===========================

Installing and configuring
--------------------------
Unpack to `protected/extensions`.

Configure application (`config/main.php`):
~~~
[php]
return array(
    'import'=>array(
        // …
        'ext.yiiext.widgets.uploadify.*',
    ),
    // …
);
~~~

Usage
-----

In a view `views/form.php`:
~~~
[php]
<?php $this->widget('EUplodifyWidget', array(
    // model
    'model' => $model,
    // attribute name
    'modelAttribute' => 'fileAttribute',
    'settings' => array(),
)); ?>
~~~
   
### Settings

Widget accepts following parameters:

- `model` — model.
- `modelAttribute` — attribute name of type `file`.
- `settings` — [Uplodify options](http://www.uploadify.com/documentation).

Example
-------

We will need:

- Form model.
- Controller action.

### Model

`UploadifyFile.php`:
~~~
[php]
class UploadifyFile extends CFormModel {
    public $uploadifyFile;

    public function rules() {
        return array(
            array('uploadifyFile', 'file',
                  'maxSize' => 1024*1024*1024,
                  'types' => 'jpg, png, gif, txt'),
        );
    }
}
~~~

### Action

You can use standard action, or following implementation of
[CAction, described in the guide](http://yiiframework.ru/doc/guide/en/basics.controller).

~~~
[php]
class SwfUploadUploadAction extends CAction {
    public $folder;

    public function run() {
        $folder = $this->folder;
        if ($folder === FALSE) {
            throw new CException(Yii::t(__CLASS__, "Folder does not exists.", array()));
        }
        if (isset($_FILES['UploadifyFile']) === TRUE) {
            $model = new UploadifyFile;
            $model->attributes = array ('uploadifyFile' => '');
            $model->uploadifyFile = CUploadedFile::getInstance($model, 'uploadifyFile');
            if ($model->validate() === FALSE) {
                throw new CException(Yii::t(__CLASS__, "Invalid file.", array()));
            }
            if (!$model->uploadifyFile->saveAs($folder.'/'.$model->uploadifyFile->getName())){
                throw new CException(Yii::t(__CLASS__, "Upload error.", array()));
            }
            else {
                die("Upload success");
            }
        }
        else {
            throw new CException(Yii::t(__CLASS__, "File not sent.", array()));
        }
        throw new CException(Yii::t(__CLASS__, 'Unknown error.', array()));
    }
}
~~~