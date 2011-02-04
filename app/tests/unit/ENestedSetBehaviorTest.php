<?php
class ENestedSetBehaviorTest extends CDbTestCase
{
	public $fixtures=array(
		'Category',
		'CategoryWithManyRoots',
	);

	public function testDescendants()
	{
		// single root
		$category=Category::model()->findByPk(1);
		$this->assertTrue($category instanceof Category);
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
			$this->assertTrue($descendant instanceof Category);
		$this->assertEquals($descendants[0]->primaryKey,2);
		$this->assertEquals($descendants[1]->primaryKey,3);
		$this->assertEquals($descendants[2]->primaryKey,4);
		$this->assertEquals($descendants[3]->primaryKey,5);
		$this->assertEquals($descendants[4]->primaryKey,6);
		$this->assertEquals($descendants[5]->primaryKey,7);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(1);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
			$this->assertTrue($descendant instanceof CategoryWithManyRoots);
		$this->assertEquals($descendants[0]->primaryKey,2);
		$this->assertEquals($descendants[1]->primaryKey,3);
		$this->assertEquals($descendants[2]->primaryKey,4);
		$this->assertEquals($descendants[3]->primaryKey,5);
		$this->assertEquals($descendants[4]->primaryKey,6);
		$this->assertEquals($descendants[5]->primaryKey,7);
	}

	public function testChildren()
	{
		// single root
		$category=Category::model()->findByPk(1);
		$this->assertTrue($category instanceof Category);
		$children=$category->children()->findAll();
		$this->assertEquals(count($children),2);
		foreach($children as $child)
			$this->assertTrue($child instanceof Category);
		$this->assertEquals($children[0]->primaryKey,2);
		$this->assertEquals($children[1]->primaryKey,5);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(1);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$children=$category->children()->findAll();
		$this->assertEquals(count($children),2);
		foreach($children as $child)
			$this->assertTrue($child instanceof CategoryWithManyRoots);
		$this->assertEquals($children[0]->primaryKey,2);
		$this->assertEquals($children[1]->primaryKey,5);
	}

	public function testAncestors()
	{
		// single root
		$category=Category::model()->findByPk(7);
		$this->assertTrue($category instanceof Category);
		$ancestors=$category->ancestors()->findAll();
		$this->assertEquals(count($ancestors),2);
		foreach($ancestors as $ancestor)
			$this->assertTrue($ancestor instanceof Category);
		$this->assertEquals($ancestors[0]->primaryKey,1);
		$this->assertEquals($ancestors[1]->primaryKey,5);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(7);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$ancestors=$category->ancestors()->findAll();
		$this->assertEquals(count($ancestors),2);
		foreach($ancestors as $ancestor)
			$this->assertTrue($ancestor instanceof CategoryWithManyRoots);
		$this->assertEquals($ancestors[0]->primaryKey,1);
		$this->assertEquals($ancestors[1]->primaryKey,5);
	}

	public function testRoots()
	{
		// single root
		$roots=Category::model()->roots()->findAll();
		$this->assertEquals(count($roots),1);
		foreach($roots as $root)
			$this->assertTrue($root instanceof Category);
		$this->assertEquals($roots[0]->primaryKey,1);

		// many roots
		$roots=CategoryWithManyRoots::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
			$this->assertTrue($root instanceof CategoryWithManyRoots);
		$this->assertEquals($roots[0]->primaryKey,1);
		$this->assertEquals($roots[1]->primaryKey,8);
	}

	public function testGetParent()
	{
		// single root
		$category=Category::model()->findByPk(4);
		$this->assertTrue($category instanceof Category);
		$parent=$category->getParent();
		$this->assertTrue($parent instanceof Category);
		$this->assertEquals($parent->primaryKey,2);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$parent=$category->getParent();
		$this->assertTrue($parent instanceof CategoryWithManyRoots);
		$this->assertEquals($parent->primaryKey,2);
	}

	public function testGetPrevSibling()
	{
		// single root
		$category=Category::model()->findByPk(7);
		$this->assertTrue($category instanceof Category);
		$sibling=$category->getPrevSibling();
		$this->assertTrue($sibling instanceof Category);
		$this->assertEquals($sibling->primaryKey,6);
		$sibling=$sibling->getPrevSibling();
		$this->assertNull($sibling);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(7);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$sibling=$category->getPrevSibling();
		$this->assertTrue($sibling instanceof CategoryWithManyRoots);
		$this->assertEquals($sibling->primaryKey,6);
		$sibling=$sibling->getPrevSibling();
		$this->assertNull($sibling);
	}

	public function testGetNextSibling()
	{
		// single root
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$sibling=$category->getNextSibling();
		$this->assertTrue($sibling instanceof Category);
		$this->assertEquals($sibling->primaryKey,7);
		$sibling=$sibling->getNextSibling();
		$this->assertNull($sibling);

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$sibling=$category->getNextSibling();
		$this->assertTrue($sibling instanceof CategoryWithManyRoots);
		$this->assertEquals($sibling->primaryKey,7);
		$sibling=$sibling->getNextSibling();
		$this->assertNull($sibling);
	}

	/**
	* @depends testDescendants
	*/
	public function testIsDescendantOf()
	{
		// single root
		$category=Category::model()->findByPk(1);
		$this->assertTrue($category instanceof Category);
		$descendants=$category->descendants()->findAll();
		foreach($descendants as $descendant)
			$this->assertTrue($descendant->isDescendantOf($category));
		$descendant=Category::model()->findByPk(4);
		$this->assertTrue($descendant instanceof Category);
		$this->assertFalse($category->isDescendantOf($descendant));

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(1);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$descendants=$category->descendants()->findAll();
		foreach($descendants as $descendant)
			$this->assertTrue($descendant->isDescendantOf($category));
		$descendant=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($descendant instanceof CategoryWithManyRoots);
		$this->assertFalse($category->isDescendantOf($descendant));
	}

	public function testIsRoot()
	{
		// single root
		$roots=Category::model()->roots()->findAll();
		$this->assertEquals(count($roots),1);
		foreach($roots as $root)
		{
			$this->assertTrue($root instanceof Category);
			$this->assertTrue($root->isRoot());
		}
		$notRoot=Category::model()->findByPk(4);
		$this->assertTrue($notRoot instanceof Category);
		$this->assertFalse($notRoot->isRoot());

		// many roots
		$roots=CategoryWithManyRoots::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
		{
			$this->assertTrue($root instanceof CategoryWithManyRoots);
			$this->assertTrue($root->isRoot());
		}
		$notRoot=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($notRoot instanceof CategoryWithManyRoots);
		$this->assertFalse($notRoot->isRoot());
	}

	public function testIsLeaf()
	{
		// single root
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$this->assertFalse($category->isLeaf());
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),2);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof Category);
			$this->assertTrue($descendant->isLeaf());
		}

		// many roots
		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertFalse($category->isLeaf());
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),2);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof CategoryWithManyRoots);
			$this->assertTrue($descendant->isLeaf());
		}
	}

	public function testSaveNode()
	{
		// single root

		// many roots
		$category=new CategoryWithManyRoots;
		$this->assertFalse($category->saveNode());
		$category->name='test';
		$this->assertTrue($category->saveNode());
		$this->assertEquals($category->root,$category->primaryKey);
		$this->assertEquals($category->lft,1);
		$this->assertEquals($category->rgt,2);
		$this->assertEquals($category->level,1);
	}

	public function testDeleteNode()
	{
		// single root
		$array=Category::model()->findAll();
		$category=Category::model()->findByPk(4);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->deleteNode());
		$this->assertTrue($this->checkTree());
		$this->assertTrue($category->getIsDeletedRecord());
		$this->assertTrue($this->checkArray($array));
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->deleteNode());
		$this->assertTrue($this->checkTree());
		$this->assertTrue($category->getIsDeletedRecord());
		$this->assertTrue($this->checkArray($array));
		foreach($array as $item)
		{
			if(in_array($item->primaryKey,array(4,5,6,7)))
				$this->assertTrue($item->getIsDeletedRecord());
			else
				$this->assertFalse($item->getIsDeletedRecord());
		}

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();
		$category=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->deleteNode());
		$this->assertTrue($this->checkTreeWithManyRoots());
		$this->assertTrue($category->getIsDeletedRecord());
		$this->assertTrue($this->checkArrayWithManyRoots($array));
		$category=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->deleteNode());
		$this->assertTrue($this->checkTreeWithManyRoots());
		$this->assertTrue($category->getIsDeletedRecord());
		$this->assertTrue($this->checkArrayWithManyRoots($array));
		foreach($array as $item)
		{
			if(in_array($item->primaryKey,array(4,9,10,11)))
				$this->assertTrue($item->getIsDeletedRecord());
			else
				$this->assertFalse($item->getIsDeletedRecord());
		}
	}

	public function testPrependTo()
	{
		// single root
		$array=Category::model()->findAll();
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$category1=new Category;
		$this->assertFalse($category1->prependTo($target));
		$category1->name='test';
		$this->assertTrue($category1->prependTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category1;
		$category2=new Category;
		$category2->name='test';
		$this->assertTrue($category2->prependTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category1=new CategoryWithManyRoots;
		$this->assertFalse($category1->prependTo($target));
		$category1->name='test';
		$this->assertTrue($category1->prependTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category1;
		$category2=new CategoryWithManyRoots;
		$category2->name='test';
		$this->assertTrue($category2->prependTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testAppendTo()
	{
		// single root
		$array=Category::model()->findAll();
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$category1=new Category;
		$this->assertFalse($category1->appendTo($target));
		$category1->name='test';
		$this->assertTrue($category1->appendTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category1;
		$category2=new Category;
		$category2->name='test';
		$this->assertTrue($category2->appendTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category1=new CategoryWithManyRoots;
		$this->assertFalse($category1->appendTo($target));
		$category1->name='test';
		$this->assertTrue($category1->appendTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category1;
		$category2=new CategoryWithManyRoots;
		$category2->name='test';
		$this->assertTrue($category2->appendTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testInsertBefore()
	{
		// single root
		$array=Category::model()->findAll();
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$category1=new Category;
		$this->assertFalse($category1->insertBefore($target));
		$category1->name='test';
		$this->assertTrue($category1->insertBefore($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category1;
		$category2=new Category;
		$category2->name='test';
		$this->assertTrue($category2->insertBefore($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category1=new CategoryWithManyRoots;
		$this->assertFalse($category1->insertBefore($target));
		$category1->name='test';
		$this->assertTrue($category1->insertBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category1;
		$category2=new CategoryWithManyRoots;
		$category2->name='test';
		$this->assertTrue($category2->insertBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testInsertAfter()
	{
		// single root
		$array=Category::model()->findAll();
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$category1=new Category;
		$this->assertFalse($category1->insertAfter($target));
		$category1->name='test';
		$this->assertTrue($category1->insertAfter($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category1;
		$category2=new Category;
		$category2->name='test';
		$this->assertTrue($category2->insertAfter($target));
		$this->assertTrue($this->checkTree());
		$array[]=$category2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category1=new CategoryWithManyRoots;
		$this->assertFalse($category1->insertAfter($target));
		$category1->name='test';
		$this->assertTrue($category1->insertAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category1;
		$category2=new CategoryWithManyRoots;
		$category2->name='test';
		$this->assertTrue($category2->insertAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$category2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveBefore()
	{
		// single root
		$array=Category::model()->findAll();

		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();

		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAfter()
	{
		// single root
		$array=Category::model()->findAll();

		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();

		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAsFirst()
	{
		// single root
		$array=Category::model()->findAll();

		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();

		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAsLast()
	{
		// single root
		$array=Category::model()->findAll();

		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=CategoryWithManyRoots::model()->findAll();

		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	private function checkTree()
	{
		return $this->checkTree1()
			&& $this->checkTree2()
			&& $this->checkTree3()
			&& $this->checkTree4();
	}

	private function checkTree1()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `Category` WHERE `lft`>=`rgt`;')->queryScalar();
	}

	private function checkTree2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `Category` WHERE NOT MOD(`rgt`-`lft`,2);')->queryScalar();
	}

	private function checkTree3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `Category` WHERE MOD(`lft`-`level`,2);')->queryScalar();
	}

	private function checkTree4()
	{
		$row=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `Category`;')->queryRow(false);

		if($row[0]!=1 || $row[1]!=$row[2]*2)
			return false;

		return true;
	}

	private function checkArray($array)
	{
		return $this->checkArray1($array)
			&& $this->checkArray2($array)
			&& $this->checkArray3($array)
			&& $this->checkArray4($array);
	}

	private function checkArray1($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && $node->lft>=$node->rgt)
				return false;
		}

		return true;
	}

	private function checkArray2($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && !(($node->rgt-$node->lft)%2))
				return false;
		}

		return true;
	}

	private function checkArray3($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && ($node->lft-$node->level)%2)
				return false;
		}

		return true;
	}

	private function checkArray4($array)
	{
		$count=0;

		foreach($array as $node)
		{
			if($node->getIsDeletedRecord())
				continue;
			else
				$count++;

			if(!isset($min) || $min>$node->lft)
				$min=$node->lft;

			if(!isset($max) || $max<$node->rgt)
				$max=$node->rgt;
		}

		if(!$count)
			return true;

		if($min!=1 || $max!=$count*2)
			return false;

		return true;
	}

	private function checkTreeWithManyRoots()
	{
		return $this->checkTreeWithManyRoots1()
			&& $this->checkTreeWithManyRoots2()
			&& $this->checkTreeWithManyRoots3()
			&& $this->checkTreeWithManyRoots4();
	}

	private function checkTreeWithManyRoots1()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE `lft`>=`rgt` GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE NOT MOD(`rgt`-`lft`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE MOD(`lft`-`level`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots4()
	{
		$rows=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `CategoryWithManyRoots` GROUP BY `root`;')->queryAll(false);

		foreach($rows as $row)
		{
			if($row[0]!=1 || $row[1]!=$row[2]*2)
				return false;
		}

		return true;
	}

	private function checkArrayWithManyRoots($array)
	{
		return $this->checkArrayWithManyRoots1($array)
			&& $this->checkArrayWithManyRoots2($array)
			&& $this->checkArrayWithManyRoots3($array)
			&& $this->checkArrayWithManyRoots4($array);
	}

	private function checkArrayWithManyRoots1($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && $node->lft>=$node->rgt)
				return false;
		}

		return true;
	}

	private function checkArrayWithManyRoots2($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && !(($node->rgt-$node->lft)%2))
				return false;
		}

		return true;
	}

	private function checkArrayWithManyRoots3($array)
	{
		foreach($array as $node)
		{
			if(!$node->getIsDeletedRecord() && ($node->lft-$node->level)%2)
				return false;
		}

		return true;
	}

	private function checkArrayWithManyRoots4($array)
	{
		$min=array();
		$max=array();
		$count=array();

		foreach($array as $n=>$node)
		{
			if($node->getIsDeletedRecord())
				continue;
			else if(isset($count[$node->root]))
				$count[$node->root]++;
			else
				$count[$node->root]=1;

			if(!isset($min[$node->root]) || $min[$node->root]>$node->lft)
				$min[$node->root]=$node->lft;

			if(!isset($max[$node->root]) || $max[$node->root]<$node->rgt)
				$max[$node->root]=$node->rgt;
		}

		foreach($count as $root=>$c)
		{
			if($min[$root]!=1 || $max[$root]!=$c*2)
				return false;
		}

		return true;
	}
}