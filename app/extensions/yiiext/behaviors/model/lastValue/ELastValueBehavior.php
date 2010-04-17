<?php
/**
 * ELastValueBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class ELastValueBehavior extends CActiveRecordBehavior
{
	protected $_attribute = NULL;
	protected $_backupAttribute = NULL;
	protected $_value = NULL;

	public function setAttribute($attribute)
	{
		if (is_string($attribute))
			$this->_attribute = $attribute;
	}
	public function setBackupAttribute($attribute)
	{
		if (is_string($attribute))
			$this->_backupAttribute = $attribute;
	}
	public function getAttribute()
	{
		if ($this->_attribute !== NULL && !$this->getOwner()->hasAttribute($this->_attribute))
			$this->_attribute = NULL;

		return $this->_attribute;
	}
	public function getBackupAttribute()
	{
		if ($this->_backupAttribute === NULL
				&& $this->getAttribute() !== NULL
				&& $this->getOwner()->hasAttribute('last_' . $this->getAttribute()))
			$this->_backupAttribute = 'last_' . $this->getAttribute();

		return $this->_backupAttribute;
	}
	public function afterFind($event)
	{
		if ($this->getAttribute() !== NULL)
			$this->_value = $this->getOwner()->getAttribute($this->getAttribute());
		
		return parent::afterFind($event);
	}
	public function beforeSave($event)
	{
		if ($this->_value !== NULL)
			$this->getOwner()->setAttribute($this->getBackupAttribute(), $this->_value);

		return parent::beforeSave($event);
	}
}
