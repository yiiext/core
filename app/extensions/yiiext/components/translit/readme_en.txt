ETranslitFilter
===============

Transliterates specified attribute value from cyrillic into latin.
Can be used to generate alias of a model from title.

Usage
-----

Add validator to model rules:
~~~
[php]
public function rules()
{
	array('alias','ext.yiiext.components.translit.ETranslitFilter','translitAttribute'=>'title'),
}
~~~
