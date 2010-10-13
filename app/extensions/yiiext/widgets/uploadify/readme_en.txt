Widget for jQuery Uploadify
===========================

Usage
-----

In a view `views/form.php`:
~~~
[php]
$this->widget('ext.yiiext.widgets.uploadify.EUploadifyWidget', array(
    // you can either use it for model attribute
    'model'=>new UploadifyFile,
	'attribute'=>'uploadifyFile',
	// or just for input field
	'name'=>'my_input_name',
    // extension [options](http://www.uploadify.com/documentation/)
	'options'=>array(
		'fileExt'=>'*.jpg;*.png;*.gif',
		'script'=>$this->createUrl('controller/action'),
		'auto'=>false,
		'multi'=>true,
		'buttonText'=>'Upload Images',
	),
));
~~~
   
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
