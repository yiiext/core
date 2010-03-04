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
		$owner=$this->getOwner();

		if($owner->getIsNewRecord() || $this->useOnUpdate)
		{
			foreach($owner->getTableSchema()->columns as $column)
			{
				if($column->allowNull && trim($owner->getAttribute($column->name))==='')
					$owner->setAttribute($column->name,null);
			}
		}
	}
}