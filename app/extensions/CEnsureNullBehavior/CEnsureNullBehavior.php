<?php
/**
* EnsureNullBehavior
*
* Сохраняет пустые аттрибуты как NULL, если по умоланию они NULL.
*
* @version 1.0
* @author creocoder <creocoder@gmail.com>
*/
class CEnsureNullBehavior extends CActiveRecordBehavior
{
	/**
	* @var bool Позволяет отключать поведение при обновлении.
	*/
	public $useOnUpdate=true;

	public function beforeSave($event)
	{
		if($this->owner->getIsNewRecord() || $this->useOnUpdate)
			foreach($this->owner->getTableSchema()->columns as $column)
				if($column->allowNull && trim($this->owner->getAttribute($column->name))==='')
					$this->owner->setAttribute($column->name,null);
	}
}