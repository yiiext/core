<?php
/**
* NestedSetBehavior
*
* TODO: изменить значение левого и правого ключа на +1
* TODO: проверять не вставляется ли нода сама в себя, то же с перемещением
* TODO: уточнить уровень в методах getPrevSibling и getNextSibling
*
* @version 0.3
* @author creocoder <creocoder@gmail.com>
*/

class CNestedSetBehavior extends CActiveRecordBehavior
{
	public $hasManyRoots=false;
	public $root='root';
	public $left='lft';
	public $right='rgt';
	public $level='level';

	/**
	* Named scope. Gets children for node (direct descendants only).
	* @return CActiveRecord the owner component.
	*/
	public function children()
	{
		return $this->descendants(1);
	}

	/**
	* Named scope. Gets descendants for node.
	* @param int depth.
	* @return CActiveRecord the owner component.
	*/
	public function descendants($depth=null)
	{
		$owner=$this->getOwner();
		$criteria=$owner->getDbCriteria();
		$alias=$criteria->alias===null ? 't' : $criteria->alias; //TODO: watch issue 914

		$criteria->mergeWith(array(
			'condition'=>$alias.'.'.$this->left.'>'.$owner->getAttribute($this->left).
				' AND '.$alias.'.'.$this->right.'<'.$owner->getAttribute($this->right),
			'order'=>$alias.'.'.$this->left,
		));

		if($depth!==null)
			$criteria->addCondition($alias.'.'.$this->level.'<='.($owner->getAttribute($this->level)+$depth));

		if($this->hasManyRoots)
			$criteria->addCondition($alias.'.'.$this->root.'='.$owner->getAttribute($this->root));

		return $owner;
	}

	/**
	* Named scope. Gets ancestors for node.
	* @param int depth.
	* @return CActiveRecord the owner component.
	*/
	public function ancestors($depth=null)
	{
		$owner=$this->getOwner();
		$criteria=$owner->getDbCriteria();
		$alias=$criteria->alias===null ? 't' : $criteria->alias; //TODO: watch issue 914

		$criteria->mergeWith(array(
			'condition'=>$alias.'.'.$this->left.'<'.$owner->getAttribute($this->left).
				' AND '.$alias.'.'.$this->right.'>'.$owner->getAttribute($this->right),
			'order'=>$alias.'.'.$this->left,
		));

		if($depth!==null)
			$criteria->addCondition($alias.'.'.$this->level.'>='.($owner->getAttribute($this->level)+$depth));

		if($this->hasManyRoots)
			$criteria->addCondition($alias.'.'.$this->root.'='.$owner->getAttribute($this->root));

		return $owner;
	}

	/**
	* Gets record of node parent.
	* @return CActiveRecord the record found.
	*/
	public function parent()
	{
		$owner=$this->getOwner();
		$criteria=$owner->getDbCriteria();
		$alias=$criteria->alias===null ? 't' : $criteria->alias; //TODO: watch issue 914

		$criteria->mergeWith(array(
			'condition'=>$alias.'.'.$this->left.'<'.$owner->getAttribute($this->left).
				' AND '.$alias.'.'.$this->right.'>'.$owner->getAttribute($this->right),
			'order'=>$alias.'.'.$this->right,
		));

		if($this->hasManyRoots)
			$criteria->addCondition($alias.'.'.$this->root.'='.$owner->getAttribute($this->root));

		return $owner->find();
	}

	//TODO: стоит отталкиваться от значения левого ключа в 1
	public function roots()
	{
		$owner=$this->getOwner();
		$criteria=$owner->getDbCriteria();
		$alias=$criteria->alias===null ? 't' : $criteria->alias; //TODO: watch issue 914

		$criteria->addCondition($alias.'.'.$this->level.'=0'); //TODO: использовать SQL отрицание ! ?

		return $owner;
	}

	public function remove()
	{
		$owner=$this->getOwner();

		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$condition=$this->left.'>='.$owner->getAttribute($this->left).' AND '.
				$this->right.'<='.$owner->getAttribute($this->right);

			$root=$this->hasManyRoots ? $owner->getAttribute($this->root) : null;

			if($root!==null)
				$condition.=' AND '.$this->root.'='.$root;

			$owner->deleteAll($condition);

			$first=$owner->getAttribute($this->right)+1;
			$delta=$owner->getAttribute($this->left)-$owner->getAttribute($this->right)-1;
			$this->shiftLeftRightValues($first,$delta,$root);

			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function append($node)
	{
		return $node->appendTo($this->getOwner());
	}

	public function appendTo($node)
	{
		$owner=$this->getOwner();

		if(!$owner->validate())
			return false;

		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$key=$node->getAttribute($this->right);
			$root=$this->hasManyRoots ? $node->getAttribute($this->root) : null;
			$this->shiftLeftRightValues($key,2,$root);
			$owner->setAttribute($this->left,$key);
			$owner->setAttribute($this->right,$key+1);
			$owner->setAttribute($this->level,$node->getAttribute($this->level)+1);

			if($root!==null)
				$owner->setAttribute($this->root,$root);

			$owner->save(false);
			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function prepend($node)
	{
		return $node->prependTo($this->getOwner());
	}

	public function prependTo($node)
	{
		$owner=$this->getOwner();

		if(!$owner->validate())
			return false;

		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$key=$node->getAttribute($this->left)+1;
			$root=$this->hasManyRoots ? $node->getAttribute($this->root) : null;
			$this->shiftLeftRightValues($key,2,$root);
			$owner->setAttribute($this->left,$key);
			$owner->setAttribute($this->right,$key+1);
			$owner->setAttribute($this->level,$node->getAttribute($this->level)+1);

			if($root!==null)
				$owner->setAttribute($this->root,$root);

			$owner->save(false);
			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function insertBefore($node)
	{
		$owner=$this->getOwner();

		if(!$owner->validate())
			return false;

		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$key=$node->getAttribute($this->left);
			$root=$this->hasManyRoots ? $node->getAttribute($this->root) : null;
			$this->shiftLeftRightValues($key,2,$root);
			$owner->setAttribute($this->left,$key);
			$owner->setAttribute($this->right,$key+1);
			$owner->setAttribute($this->level,$node->getAttribute($this->level));

			if($root!==null)
				$owner->setAttribute($this->root,$root);

			$owner->save(false);
			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function insertAfter($node)
	{
		$owner=$this->getOwner();

		if(!$owner->validate())
			return false;

		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$key=$node->getAttribute($this->right)+1;
			$root=$this->hasManyRoots ? $node->getAttribute($this->root) : null;
			$this->shiftLeftRightValues($key,2,$root);
			$owner->setAttribute($this->left,$key);
			$owner->setAttribute($this->right,$key+1);
			$owner->setAttribute($this->level,$node->getAttribute($this->level));

			if($root!==null)
				$owner->setAttribute($this->root,$root);

			$owner->save(false);
			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function isLeaf()
	{
		return $this->getOwner()->getAttribute($this->right)-$this->getOwner()->getAttribute($this->left)===1;
	}

	//TODO: стоит отталкиваться от значения левого ключа в 1
	public function isRoot()
	{
		return !(boolean)$this->getOwner()->getAttribute($this->level);
	}

	public function getPrevSibling()
	{
		$owner=$this->getOwner();
		$condition=$this->right.'='.$owner->getAttribute($this->left)-1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

		return $owner->find($condition);
	}

	public function getNextSibling()
	{
		$owner=$this->getOwner();
		$condition=$this->left.'='.$owner->getAttribute($this->right)+1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

		return $owner->find($condition);
	}

	public function createRoot()
	{
		//TODO: (creocoder)Не забыть выкидывать CException при $this->hasManyRoots==false. Категория для Yii::t() - 'yiiext'.
	}

	protected function shiftLeftRightValues($first,$delta,$root=null)
	{
		$owner=$this->getOwner();

		foreach(array($this->left,$this->right) as $key)
		{
			$condition=$key.'>='.$first;

			if($root!==null)
				$condition.=' AND '.$this->root.'='.$root;

			$owner->updateAll(array($key=>new CDbExpression($key.sprintf('%+d',$delta))),$condition);
		}
	}

	protected function saveNode($left,$right,$level)
	{

	}
}