<?php
/**
* EnsureNullBehavior
*
* Ensures no empty AR property value is written to DB if property's default is `NULL`.
*
* @version 1.0
* @author creocoder <creocoder@gmail.com>
*/
class CEnsureNullBehavior extends CActiveRecordBehavior
{
	/**
	* @var bool Ensure nulls on update
	*/
	public $useOnUpdate=true;

	public function beforeSave($event)
	{
		if($this->owner->getIsNewRecord() || $this->useOnUpdate)
		{
			foreach($this->owner->getTableSchema()->columns as $column)
			{
				if($column->allowNull && trim($this->owner->getAttribute($column->name))==='')
					$this->owner->setAttribute($column->name,null);
			}
		}
	}
}