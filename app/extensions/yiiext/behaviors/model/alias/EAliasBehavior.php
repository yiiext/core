<?php
/**
 * EAliasBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class EAliasBehavior extends CActiveRecordBehavior
{
	protected $_attribute = 'alias';
	protected $_copyAttribute = NULL; //'title';

	public function setAttribute($attribute)
	{
		$this->_attribute = $attribute;
	}
	public function getAttribute()
	{
		if ($this->_attribute !== NULL && !$this->getOwner()->hasAttribute($this->_attribute))
			$this->_attribute = NULL;

		return $this->_attribute;
	}
	public function setCopyAttribute($attribute)
	{
		$this->_copyAttribute = $attribute;
	}
	public function getCopyAttribute()
	{
		if ($this->_copyAttribute !== NULL && !$this->getOwner()->hasAttribute($this->_copyAttribute))
			$this->_copyAttribute = NULL;

		return $this->_copyAttribute;
	}
	public function beforeValidate($event)
	{
		$owner = $this->getOwner();
		$attribute = $this->getAttribute();
		$copyAttribute = $this->getCopyAttribute();
		
		$value = ($copyAttribute !== NULL && $owner->getAttribute($attribute) == '' ? $owner->getAttribute($copyAttribute) : $owner->getAttribute($attribute));

		$owner->setAttribute($attribute, self::translit($value));

		return parent::beforeValidate($event);
	}
	protected static function translit($str) { 
		$replacement = array(
			"й" => "i", "ц" => "c", "у" => "u", "к" => "k", "е" => "e", "н" => "n",
			"г" => "g", "ш" => "sh", "щ" => "sh", "з" => "z", "х" => "h", "ъ" => "\'",
			"ф" => "f", "ы" => "i", "в" => "v", "а" => "a", "п" => "p", "р" => "r",
			"о" => "o", "л" => "l", "д" => "d", "ж" => "zh", "э" => "ie", "ё" => "e",
			"я" => "ya", "ч" => "ch", "с" => "c", "м" => "m", "и" => "i", "т" => "t",
			"ь" => "\'", "б" => "b", "ю" => "yu",
			"Й" => "I", "Ц" => "C", "У" => "U", "К" => "K", "Е" => "E", "Н" => "N",
			"Г" => "G", "Ш" => "SH", "Щ" => "SH", "З" => "Z", "Х" => "X", "Ъ" => "\'",
			"Ф" => "F", "Ы" => "I", "В" => "V", "А" => "A", "П" => "P", "Р" => "R",
			"О" => "O", "Л" => "L", "Д" => "D", "Ж" => "ZH", "Э" => "IE", "Ё" => "E",
			"Я" => "YA", "Ч" => "CH", "С" => "C", "М" => "M", "И" => "I", "Т" => "T",
			"Ь" => "\'", "Б" => "B", "Ю" => "YU",
			"«" => "", "»" => "", " " => "_", 
		);
		foreach ($replacement as $i => $u)
			$str = mb_eregi_replace($i, $u, $str);

		return $str;
	}
}
