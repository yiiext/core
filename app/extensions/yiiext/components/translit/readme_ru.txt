ETranslitFilter
===============

Транслитерация значения атрибута, записанного не латиницей, латинскими буквами.
Например, можно использовать для автоматического создания псевдонима из заголовка модели.

Использование
-------------
Добавляем валидатор в правила модели:
~~~
[php]
public function rules()
{
	array('alias','ext.yiiext.components.translit.ETranslitFilter','translitAttribute'=>'title'),
}
~~~
