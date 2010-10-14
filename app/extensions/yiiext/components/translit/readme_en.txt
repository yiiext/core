ETranslitFilter.
===============

Translit the specified attribute value from cyrillic to latin letters.
Example, can use for generate alias of models from title.

Usage
-------------
Add validator to model rules:
~~~
[php]
public function rules()
{
	array('alias','ext.yiiext.components.translit.ETranslitFilter','translitAttribute'=>'title'),
}
~~~
