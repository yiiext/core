Обёртка для плагина jQuery Uploadify
====================================

Используем в представлении `views/form.php`:
~~~
[php]
$this->widget('ext.yiiext.widgets.uploadify.EUploadifyWidget', array(
	// можно использовать как для поля модели
	'model'=>new UploadifyFile,
	'attribute'=>'uploadifyFile',
	// так и просто для элемента формы
	'name'=>'my_input_name',
	// Имя POST-параметра, через который будет посылаться ИД сессии
	'sessionParam'=>'PHP_SESSION_ID',
	// [настройки](http://www.uploadify.com/documentation/) плагина
	'options'=>array(
		'fileExt'=>'*.jpg;*.png;*.gif',
		'script'=>$this->createUrl('controller/action'),
		'auto'=>false,
		'multi'=>true,
		'buttonText'=>'Upload Images',
	),
));
~~~

Пример использования
--------------------

Для использования нам нужны:

- Модель формы.
- Действие контроллера.

### Пример модели

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

### Пример действия

Действие можно описать и как обычное действие контроллера, но здесь мы используем
[CAction, описанный в полном руководстве](http://yiiframework.ru/doc/guide/ru/basics.controller).

~~~
[php]
class SwfUploadAction extends CAction {
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
