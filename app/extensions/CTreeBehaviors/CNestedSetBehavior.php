<?php
/**
 * NestedSetBehavior
 *
 * TODO: изменить значение левого и правого ключа на +1
 * TODO: проверять существование цели в appendTo,prependTo,insertBefore,insertAfter?
 *
 * @version 0.4
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
	 * Named scope. Gets descendants for node.
	 * @param int depth.
	 * @return CActiveRecord the owner.
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
	 * Named scope. Gets children for node (direct descendants only).
	 * @return CActiveRecord the owner.
	 */
	public function children()
	{
		return $this->descendants(1);
	}

	/**
	 * Named scope. Gets ancestors for node.
	 * @param int depth.
	 * @return CActiveRecord the owner.
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
	 * Named scope. Gets root node(s).
	 * @param int depth.
	 * @return CActiveRecord the owner.
	 */
	public function roots()
	{
		$owner=$this->getOwner();
		$criteria=$owner->getDbCriteria();
		$alias=$criteria->alias===null ? 't' : $criteria->alias; //TODO: watch issue 914

		$criteria->addCondition($alias.'.'.$this->level.'=0'); //TODO: стоит отталкиваться от значения левого ключа в 1

		return $owner;
	}

	/**
	 * Gets record of node parent.
	 * @return CActiveRecord the record found. Null if no record is found.
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

	/**
	 * Gets record of previous sibling.
	 * @return CActiveRecord the record found. Null if no record is found.
	 */
	public function getPrevSibling() //TODO: переименовать просто в prev()?
	{
		$owner=$this->getOwner();
		$condition=$this->right.'='.$owner->getAttribute($this->left)-1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

		return $owner->find($condition);
	}

	/**
	 * Gets record of next sibling.
	 * @return CActiveRecord the record found. Null if no record is found.
	 */
	public function getNextSibling() //TODO: переименовать просто в next()?
	{
		$owner=$this->getOwner();
		$condition=$this->left.'='.$owner->getAttribute($this->right)+1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

		return $owner->find($condition);
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

	/**
	 * Appends node to owner as last child.
	 * @return boolean whether the appending succeeds.
	 * @throws CException if the target node is self.
	 */
	public function append($node,$runValidation=true)
	{
		return $node->appendTo($this->getOwner(),$runValidation);
	}

	/**
	 * Appends owner to node as last child.
	 * @return boolean whether the appending succeeds.
	 * @throws CException if the target node is self.
	 */
	public function appendTo($node,$runValidation=true)
	{
		$owner=$this->getOwner();

		if($owner===$node)
			throw new CException(Yii::t('yiiext','The target node should not be self.'));

		if($runValidation && !$owner->validate())
			return false;

		$db=$owner->getDbConnection();
		$extTransFlag=$db->getCurrentTransaction();

		if($extTransFlag===null)
			$transaction=$db->beginTransaction();

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

			if($extTransFlag===null)
				$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			if($extTransFlag===null)
				$transaction->rollBack();

			return false;
		}
	}

	/**
	 * Prepends node to owner as first child.
	 * @return boolean whether the prepending succeeds.
	 * @throws CException if the target node is self.
	 */
	public function prepend($node,$runValidation=true)
	{
		return $node->prependTo($this->getOwner(),$runValidation);
	}

	/**
	 * Prepends owner to node as first child.
	 * @return boolean whether the prepending succeeds.
	 * @throws CException if the target node is self.
	 */
	public function prependTo($node,$runValidation=true)
	{
		$owner=$this->getOwner();

		if($owner===$node)
			throw new CException(Yii::t('yiiext','The target node should not be self.'));

		if($runValidation && !$owner->validate())
			return false;

		$db=$owner->getDbConnection();
		$extTransFlag=$db->getCurrentTransaction();

		if($extTransFlag===null)
			$transaction=$db->beginTransaction();

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

			if($extTransFlag===null)
				$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			if($extTransFlag===null)
				$transaction->rollBack();

			return false;
		}
	}

	/**
	 * Inserts owner as previous sibling of node.
	 * @return boolean whether the inserting succeeds.
	 * @throws CException if the target node is self or target node is root.
	 */
	public function insertBefore($node,$runValidation=true)
	{
		if($node->isRoot())
			throw new CException(Yii::t('yiiext','The target node should not be root.'));

		$owner=$this->getOwner();

		if($owner===$node)
			throw new CException(Yii::t('yiiext','The target node should not be self.'));

		if($runValidation && !$owner->validate())
			return false;

		$db=$owner->getDbConnection();
		$extTransFlag=$db->getCurrentTransaction();

		if($extTransFlag===null)
			$transaction=$db->beginTransaction();

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

			if($extTransFlag===null)
				$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			if($extTransFlag===null)
				$transaction->rollBack();

			return false;
		}
	}

	/**
	 * Inserts owner as next sibling of node.
	 * @return boolean whether the inserting succeeds.
	 * @throws CException if the target node is self or target node is root.
	 */
	public function insertAfter($node,$runValidation=true)
	{
		if($node->isRoot())
			throw new CException(Yii::t('yiiext','The target node should not be root.'));

		$owner=$this->getOwner();

		if($owner===$node)
			throw new CException(Yii::t('yiiext','The target node should not be self.'));

		if($runValidation && !$owner->validate())
			return false;

		$db=$owner->getDbConnection();
		$extTransFlag=$db->getCurrentTransaction();

		if($extTransFlag===null)
			$transaction=$db->beginTransaction();

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

			if($extTransFlag===null)
				$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			if($extTransFlag===null)
				$transaction->rollBack();

			return false;
		}
	}

	public function isLeaf()
	{
		return $this->getOwner()->getAttribute($this->right)-$this->getOwner()->getAttribute($this->left)===1;
	}

	public function isRoot()
	{
		return !(boolean)$this->getOwner()->getAttribute($this->level); //TODO: стоит отталкиваться от значения левого ключа в 1
	}

	/**
	 * Create root node. Only used in multiple-root trees.
	 * @return boolean whether the creating succeeds.
	 * @throws CException if many root mode is off.
	 */
	public function createRoot($runValidation=true)
	{
		if(!$this->hasManyRoots)
			throw new CException(Yii::t('yiiext','Many roots mode is off.'));

		$owner=$this->getOwner();

		if($runValidation && !$owner->validate())
			return false;

		$db=$owner->getDbConnection();
		$extTransFlag=$db->getCurrentTransaction();

		if($extTransFlag===null)
			$transaction=$db->beginTransaction();

		try
		{
			$owner->setAttribute($this->left,1);
			$owner->setAttribute($this->right,2);
			$owner->setAttribute($this->level,0);
			$owner->save(false);
			$owner->setAttribute($this->root,$owner->getPrimaryKey());
			$owner->save(false);

			if($extTransFlag===null)
				$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			if($extTransFlag===null)
				$transaction->rollBack();

			return false;
		}
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