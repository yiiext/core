Image Validator
=============
Компонент для валидации картинок, ширины, высота, mime тип. Комонент унаследован от
CFileValidate по этому содержит все возможности этого валидатора, например размер файла,
расширение.

Установка
---------------------
В `protected/config/main.php` добавить:
~~~
[php]
'import'=>array(
    'ext.yiiext.components.imageValidator.*'
),
~~~

Использование
---------------------
public function rules()
{
	return array(
		array('inputname', 'EImageValidator', 'on'=>'a'),
	);
}