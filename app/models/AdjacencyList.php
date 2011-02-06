<?php
class AdjacencyList extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors()
	{
		return array(
			'tree'=>array(
				'class'=>'ext.yiiext.behaviors.model.trees.al.EAdjacencyListBehavior',
			),
		);
	}

	public function rules()
	{
		return array(
			array('name','required'),
		);
	}
}
