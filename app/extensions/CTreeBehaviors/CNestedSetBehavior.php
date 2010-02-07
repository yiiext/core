<?php
/**
* Основная мотивация написания, это то, что этот класс, в отличие от того,
* что в extensions позволит вытаскивать категории вместе со связанными моделями.
*
* Например:
* $criteria=new CDbCriteria(array(
* 	'condition'=>'products.weight>?',
* 	'params'=>array(40),
* 	'with'=>array(
* 		'products'=>array(
* 			'joinType'=>'INNER JOIN',
* 		),
* 	)
* ));
* $category->findChilds($criteria);
* вытащит только те категории продкуты в которых весят больше 40г
*
* Если делать через named space, то код выглядит ещё проще и CDbCriteria можно не создавать.
*
* Далее идут задания себе. Советы с аргументацией можно давать указывая в скобках ник.
*
* Например:
* TODO: (creocoder)нужен ли при варианте findParents(findPath) реализованном в виде named space?
* TODO: (someone)По идее нет, родителя можно вытащить так $category->pathOf()->find(). Но это для искушенных.
*
* Поехали:
* TODO: (creocoder)проверить все(!) возможные исключения
* TODO: (creocoder)написать тесты
* TODO: (creocoder)реализовать актуализацию дерева в уже созданных во время выполнения объектах. Через Yii Events, либо паттер Registry?
* TODO: (creocoder)наследоваться от CTreeBehavior или реализовывать интерфейс ITree? (преимущества, недостатки)
*       (samdark)занятней всего будет выделить общий функционал в abstract CTreeBehaviour для последующей реализации других способов хранение дерева 
* TODO: (creocoder)доработать с учетом 't' префикса
* TODO: (creocoder)смущает постоянная проверка $this->hasManyRoots. Вынести этот функционал в дочерний CNestedSetWithManyRootsBehavior,
* что ускорит работу методов, когда не нужны множественные корни?
*       (samdark) На скорости сильно не скажется. Можно оставить как есть.
* TODO: (creocoder)реализовать поддержку множественных корней находящихся в другой таблице? 
* TODO: (creocoder)использовать ли quoteColumn() метод для обрамления всех полей в кавычки? (распухнут 'condition')
*
* TODO: (creocoder)-== в перспективе ==-
* TODO: (creocoder)реализовать CAjListBehavior + CAjListWithManyRootsBehavior
* TODO: (creocoder)реализовать возможность конвертирования одного дерева в другое
* TODO: (creocoder)реализовать методы для тестирования целостности дерева
* TODO: (creocoder)DAO? :-)
*/

class CNestedSetBehavior extends CActiveRecordBehavior
{
    /**
     * @var boolean Хранится ли в таблице более одного дерева
     */
	public $hasManyRoots=false;

	public $root='root';
	public $left='lft';
	public $right='rgt';
	public $level='level';

	//TODO: (creocoder)реализовать в виде named space и/или добавить $criteria, как аргумент
    // (samdark) нет слова Childs, есть Children
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
    // (samdark): да.
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
    // (samdark) да.
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

	public function appendChild($node)
	{
		if(!$node->validate())
			return false;

		$transaction=$this->owner->getDbConnection()->beginTransaction();

		try
		{
			$condition=$this->left.'>'.$this->owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

			$this->owner->updateAll(array($this->left=>new CDbExpression($this->left.'+2')),$condition);

			$condition=$this->right.'>='.$this->owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

			$this->owner->updateAll(array($this->right=>new CDbExpression($this->right.'+2')),$condition);

			$node->{$this->left}=$this->owner->getAttribute($this->right);
			$node->{$this->right}=$this->owner->getAttribute($this->right)+1;
			$node->{$this->level}=$this->owner->getAttribute($this->level)+1;

			if($this->hasManyRoots)
				$node->{$this->root}=$this->owner->getAttribute($this->root);

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

	//TODO: (creocoder)есть возможность сделать через beforeDelete() возвращающий в итоге false, тогда будет возможно
	// просто $node->delete(), но повлияет на нижестоящие в behaviors() поведения, обсудить
    // (samdark) можно просто перекрыть delete() без вызова parent::delete().
    // Обязательно в этом случае триггернуть события onBeforeDelete и onAfterDelete.
	//TODO: (creocoder)учесть возможность переноса дочерних категорий на уровень вверх, вместо их удаления
    // (samdark) бесполезная фича. Если удаляют, вложенные тоже убивают.
	public function deleteNode()
	{
		$transaction=$this->owner->getDbConnection()->beginTransaction();

		try
		{
			$condition=$this->left.'>='.$this->owner->getAttribute($this->left).' AND '.
				$this->right.'<='.$this->owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

			$this->owner->deleteAll($condition);

			$width=$this->owner->getAttribute($this->right)-$this->owner->getAttribute($this->left)+1;

			$condition=$this->left.'>'.$this->owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

			$this->owner->updateAll(array($this->left=>new CDbExpression($this->left.'-'.$width)),$condition);

			$condition=$this->right.'>'.$this->owner->getAttribute($this->right);

			if($this->hasManyRoots)
				$condition.=' AND '.$this->root.'='.$this->owner->getAttribute($this->root);

			$this->owner->updateAll(array($this->right=>new CDbExpression($this->right.'-'.$width)),$condition);

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
		return $this->owner->getAttribute($this->right)-$this->owner->getAttribute($this->left)==1;
	}

	//TODO: (creocoder)может лучше return (bool)$this->owner->getAttribute($this->level)?
    // (samdark) да.
	public function isRoot()
	{
		return $this->owner->getAttribute($this->level)==0;
	}

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
}