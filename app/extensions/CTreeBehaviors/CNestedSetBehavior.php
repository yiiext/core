<?php
/**
* NestedSetBehavior
*
* @version 0.2
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

	public function appendChild($node)
	{
		if(!$node->validate())
			return false;

		$owner=$this->getOwner();
		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$condition=$this->left.'>'.$owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

			$owner->updateAll(array($this->left=>new CDbExpression($this->left.'+2')),$condition);
			$condition=$this->right.'>='.$owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

			$owner->updateAll(array($this->right=>new CDbExpression($this->right.'+2')),$condition);
			$node->setAttribute($this->left,$owner->getAttribute($this->right));
			$node->setAttribute($this->right,$owner->getAttribute($this->right)+1);
			$node->setAttribute($this->level,$owner->getAttribute($this->level)+1);

			if($this->hasManyRoots)
				$node->setAttribute($this->root,$owner->getAttribute($this->root));

			$node->save(false);
			$transaction->commit();

			return true;
		}
		catch(Exception $e)
		{
			$transaction->rollBack();

			return false;
		}
	}

	public function deleteNode()
	{
		$owner=$this->getOwner();
		$transaction=$owner->getDbConnection()->beginTransaction();

		try
		{
			$condition=$this->left.'>='.$owner->getAttribute($this->left).' AND '.
				$this->right.'<='.$owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

			$owner->deleteAll($condition);
			$width=$owner->getAttribute($this->right)-$owner->getAttribute($this->left)+1;
			$condition=$this->left.'>'.$owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

			$owner->updateAll(array($this->left=>new CDbExpression($this->left.'-'.$width)),$condition);
			$condition=$this->right.'>'.$owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$owner->getAttribute($this->root);

			$owner->updateAll(array($this->right=>new CDbExpression($this->right.'-'.$width)),$condition);
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

	public function isRoot()
	{
		return !(boolean)$this->getOwner()->getAttribute($this->level);
	}


	/* Begin all deprecated */

	//TODO: (creocoder)реализовать в виде named space и/или добавить $criteria, как аргумент
	public function findChilds()
	{
		$criteria=new CDbCriteria(array(
			'condition'=>$this->left.'>'.$this->owner->getAttribute($this->left).' AND '.
				$this->right.'<'.$this->owner->getAttribute($this->right).' AND '.
				$this->level.'='.($this->owner->getAttribute($this->level)+1),
			'order'=>$this->left,
		));

		if($this->hasManyRoots)
			$criteria->condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

		return $this->owner->findAll($criteria);
	}

	//TODO: (creocoder)переименовать в findDescendants()?
	//TODO: (creocoder)реализовать в виде named space и/или добавить $criteria, как аргумент
	public function findAllChilds()
	{
		$criteria=new CDbCriteria(array(
			'condition'=>$this->left.'>'.$this->owner->getAttribute($this->left).' AND '.
				$this->right.'<'.$this->owner->getAttribute($this->right),
			'order'=>$this->left,
		));

		if($this->hasManyRoots)
			$criteria->condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

		return $this->owner->findAll($criteria);
	}

	//TODO: (creocoder)переименовать в findPath()?
	//TODO: (creocoder)реализовать в виде named space и/или добавить $criteria, как аргумент
	public function findParents()
	{
		$criteria=new CDbCriteria(array(
			'condition'=>$this->left.'<'.$this->owner->getAttribute($this->left).' AND '.
				$this->right.'>'.$this->owner->getAttribute($this->right),
			'order'=>$this->left,
		));

		if($this->hasManyRoots)
			$criteria->condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

		return $this->owner->findAll($criteria);
	}

	//TODO: (creocoder)нужен ли при варианте findParents(findPath) реализованном в виде named space?
	public function findParent()
	{
		return;
	}


	//TODO: (creocoder)есть возможность сделать через beforeDelete() возвращающий в итоге false, тогда будет возможно
	// просто $node->delete(), но повлияет на нижестоящие в behaviors() поведения, обсудить
	//TODO: (creocoder)учесть возможность переноса дочерних категорий на уровень вверх, вместо их удаления
	//TODO: (creocoder)уточнить про уровень
	public function getPrevSibling()
	{
		$condition=$this->right.'='.$this->owner->getAttribute($this->left)-1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

		return $this->owner->find($condition);
	}

	//TODO: (creocoder)уточнить про уровень
	public function getNextSibling()
	{
		$condition=$this->left.'='.$this->owner->getAttribute($this->right)+1;

		if($this->hasManyRoots)
			$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

		return $this->owner->find($condition);
	}

	//TODO: (creocoder)реализовать действия по переносу нод
	//public function moveAs*
	//{

	//}

	//TODO: (creocoder)реализовать действие по созданию корня, в случае одобрения CNestedSetWithManyRootsBehavior - выкинуть из класса
	public function createRoot()
	{
		//TODO: (creocoder)Не забыть выкидывать CException при $this->hasManyRoots==false. Категория для Yii::t() - 'yiiext'.
	}

	/* End all deprecated */
}