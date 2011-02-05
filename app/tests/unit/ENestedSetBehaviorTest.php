<?php
class ENestedSetBehaviorTest extends CDbTestCase
{
	public $fixtures=array(
		'NestedSet',
		'NestedSetWithManyRoots',
	);

	public function testDescendants()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$descendants=$NestedSet->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
			$this->assertTrue($descendant instanceof NestedSet);
		$this->assertEquals($descendants[0]->primaryKey,2);
		$this->assertEquals($descendants[1]->primaryKey,3);
		$this->assertEquals($descendants[2]->primaryKey,4);
		$this->assertEquals($descendants[3]->primaryKey,5);
		$this->assertEquals($descendants[4]->primaryKey,6);
		$this->assertEquals($descendants[5]->primaryKey,7);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$descendants=$NestedSet->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
			$this->assertTrue($descendant instanceof NestedSetWithManyRoots);
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
		$NestedSet=NestedSet::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$children=$NestedSet->children()->findAll();
		$this->assertEquals(count($children),2);
		foreach($children as $child)
			$this->assertTrue($child instanceof NestedSet);
		$this->assertEquals($children[0]->primaryKey,2);
		$this->assertEquals($children[1]->primaryKey,5);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$children=$NestedSet->children()->findAll();
		$this->assertEquals(count($children),2);
		foreach($children as $child)
			$this->assertTrue($child instanceof NestedSetWithManyRoots);
		$this->assertEquals($children[0]->primaryKey,2);
		$this->assertEquals($children[1]->primaryKey,5);
	}

	public function testAncestors()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(7);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$ancestors=$NestedSet->ancestors()->findAll();
		$this->assertEquals(count($ancestors),2);
		foreach($ancestors as $ancestor)
			$this->assertTrue($ancestor instanceof NestedSet);
		$this->assertEquals($ancestors[0]->primaryKey,1);
		$this->assertEquals($ancestors[1]->primaryKey,5);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(7);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$ancestors=$NestedSet->ancestors()->findAll();
		$this->assertEquals(count($ancestors),2);
		foreach($ancestors as $ancestor)
			$this->assertTrue($ancestor instanceof NestedSetWithManyRoots);
		$this->assertEquals($ancestors[0]->primaryKey,1);
		$this->assertEquals($ancestors[1]->primaryKey,5);
	}

	public function testRoots()
	{
		// single root
		$roots=NestedSet::model()->roots()->findAll();
		$this->assertEquals(count($roots),1);
		foreach($roots as $root)
			$this->assertTrue($root instanceof NestedSet);
		$this->assertEquals($roots[0]->primaryKey,1);

		// many roots
		$roots=NestedSetWithManyRoots::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
			$this->assertTrue($root instanceof NestedSetWithManyRoots);
		$this->assertEquals($roots[0]->primaryKey,1);
		$this->assertEquals($roots[1]->primaryKey,8);
	}

	public function testGetParent()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(4);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$parent=$NestedSet->getParent();
		$this->assertTrue($parent instanceof NestedSet);
		$this->assertEquals($parent->primaryKey,2);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(4);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$parent=$NestedSet->getParent();
		$this->assertTrue($parent instanceof NestedSetWithManyRoots);
		$this->assertEquals($parent->primaryKey,2);
	}

	public function testGetPrevSibling()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(7);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$sibling=$NestedSet->getPrevSibling();
		$this->assertTrue($sibling instanceof NestedSet);
		$this->assertEquals($sibling->primaryKey,6);
		$sibling=$sibling->getPrevSibling();
		$this->assertNull($sibling);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(7);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$sibling=$NestedSet->getPrevSibling();
		$this->assertTrue($sibling instanceof NestedSetWithManyRoots);
		$this->assertEquals($sibling->primaryKey,6);
		$sibling=$sibling->getPrevSibling();
		$this->assertNull($sibling);
	}

	public function testGetNextSibling()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$sibling=$NestedSet->getNextSibling();
		$this->assertTrue($sibling instanceof NestedSet);
		$this->assertEquals($sibling->primaryKey,7);
		$sibling=$sibling->getNextSibling();
		$this->assertNull($sibling);

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$sibling=$NestedSet->getNextSibling();
		$this->assertTrue($sibling instanceof NestedSetWithManyRoots);
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
		$NestedSet=NestedSet::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$descendants=$NestedSet->descendants()->findAll();
		foreach($descendants as $descendant)
			$this->assertTrue($descendant->isDescendantOf($NestedSet));
		$descendant=NestedSet::model()->findByPk(4);
		$this->assertTrue($descendant instanceof NestedSet);
		$this->assertFalse($NestedSet->isDescendantOf($descendant));

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(1);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$descendants=$NestedSet->descendants()->findAll();
		foreach($descendants as $descendant)
			$this->assertTrue($descendant->isDescendantOf($NestedSet));
		$descendant=NestedSetWithManyRoots::model()->findByPk(4);
		$this->assertTrue($descendant instanceof NestedSetWithManyRoots);
		$this->assertFalse($NestedSet->isDescendantOf($descendant));
	}

	public function testIsRoot()
	{
		// single root
		$roots=NestedSet::model()->roots()->findAll();
		$this->assertEquals(count($roots),1);
		foreach($roots as $root)
		{
			$this->assertTrue($root instanceof NestedSet);
			$this->assertTrue($root->isRoot());
		}
		$notRoot=NestedSet::model()->findByPk(4);
		$this->assertTrue($notRoot instanceof NestedSet);
		$this->assertFalse($notRoot->isRoot());

		// many roots
		$roots=NestedSetWithManyRoots::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
		{
			$this->assertTrue($root instanceof NestedSetWithManyRoots);
			$this->assertTrue($root->isRoot());
		}
		$notRoot=NestedSetWithManyRoots::model()->findByPk(4);
		$this->assertTrue($notRoot instanceof NestedSetWithManyRoots);
		$this->assertFalse($notRoot->isRoot());
	}

	public function testIsLeaf()
	{
		// single root
		$NestedSet=NestedSet::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertFalse($NestedSet->isLeaf());
		$descendants=$NestedSet->descendants()->findAll();
		$this->assertEquals(count($descendants),2);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof NestedSet);
			$this->assertTrue($descendant->isLeaf());
		}

		// many roots
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertFalse($NestedSet->isLeaf());
		$descendants=$NestedSet->descendants()->findAll();
		$this->assertEquals(count($descendants),2);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof NestedSetWithManyRoots);
			$this->assertTrue($descendant->isLeaf());
		}
	}

	public function testSaveNode()
	{
		// single root

		// many roots
		$NestedSet=new NestedSetWithManyRoots;
		$this->assertFalse($NestedSet->saveNode());
		$NestedSet->name='test';
		$this->assertTrue($NestedSet->saveNode());
		$this->assertEquals($NestedSet->root,$NestedSet->primaryKey);
		$this->assertEquals($NestedSet->lft,1);
		$this->assertEquals($NestedSet->rgt,2);
		$this->assertEquals($NestedSet->level,1);
	}

	public function testDeleteNode()
	{
		// single root
		$array=NestedSet::model()->findAll();
		$NestedSet=NestedSet::model()->findByPk(4);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->deleteNode());
		$this->assertTrue($this->checkTree());
		$this->assertTrue($NestedSet->getIsDeletedRecord());
		$this->assertTrue($this->checkArray($array));
		$NestedSet=NestedSet::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->deleteNode());
		$this->assertTrue($this->checkTree());
		$this->assertTrue($NestedSet->getIsDeletedRecord());
		$this->assertTrue($this->checkArray($array));
		foreach($array as $item)
		{
			if(in_array($item->primaryKey,array(4,5,6,7)))
				$this->assertTrue($item->getIsDeletedRecord());
			else
				$this->assertFalse($item->getIsDeletedRecord());
		}

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(4);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->deleteNode());
		$this->assertTrue($this->checkTreeWithManyRoots());
		$this->assertTrue($NestedSet->getIsDeletedRecord());
		$this->assertTrue($this->checkArrayWithManyRoots($array));
		$NestedSet=NestedSetWithManyRoots::model()->findByPk(9);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->deleteNode());
		$this->assertTrue($this->checkTreeWithManyRoots());
		$this->assertTrue($NestedSet->getIsDeletedRecord());
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
		$array=NestedSet::model()->findAll();
		$target=NestedSet::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSet);
		$NestedSet1=new NestedSet;
		$this->assertFalse($NestedSet1->prependTo($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->prependTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSet;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->prependTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();
		$target=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$NestedSet1=new NestedSetWithManyRoots;
		$this->assertFalse($NestedSet1->prependTo($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->prependTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSetWithManyRoots;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->prependTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testAppendTo()
	{
		// single root
		$array=NestedSet::model()->findAll();
		$target=NestedSet::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSet);
		$NestedSet1=new NestedSet;
		$this->assertFalse($NestedSet1->appendTo($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->appendTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSet;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->appendTo($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();
		$target=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$NestedSet1=new NestedSetWithManyRoots;
		$this->assertFalse($NestedSet1->appendTo($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->appendTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSetWithManyRoots;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->appendTo($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testInsertBefore()
	{
		// single root
		$array=NestedSet::model()->findAll();
		$target=NestedSet::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSet);
		$NestedSet1=new NestedSet;
		$this->assertFalse($NestedSet1->insertBefore($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->insertBefore($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSet;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->insertBefore($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();
		$target=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$NestedSet1=new NestedSetWithManyRoots;
		$this->assertFalse($NestedSet1->insertBefore($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->insertBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSetWithManyRoots;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->insertBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testInsertAfter()
	{
		// single root
		$array=NestedSet::model()->findAll();
		$target=NestedSet::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSet);
		$NestedSet1=new NestedSet;
		$this->assertFalse($NestedSet1->insertAfter($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->insertAfter($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSet;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->insertAfter($target));
		$this->assertTrue($this->checkTree());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();
		$target=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$NestedSet1=new NestedSetWithManyRoots;
		$this->assertFalse($NestedSet1->insertAfter($target));
		$NestedSet1->name='test';
		$this->assertTrue($NestedSet1->insertAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet1;
		$NestedSet2=new NestedSetWithManyRoots;
		$NestedSet2->name='test';
		$this->assertTrue($NestedSet2->insertAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());
		$array[]=$NestedSet2;
		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveBefore()
	{
		// single root
		$array=NestedSet::model()->findAll();

		$NestedSet=NestedSet::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$target=NestedSet::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSet);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$NestedSet=NestedSet::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveBefore($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAfter()
	{
		// single root
		$array=NestedSet::model()->findAll();

		$NestedSet=NestedSet::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$target=NestedSet::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$NestedSet=NestedSet::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAfter($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAsFirst()
	{
		// single root
		$array=NestedSet::model()->findAll();

		$NestedSet=NestedSet::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$target=NestedSet::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$NestedSet=NestedSet::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(6);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsFirst($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));
	}

	public function testMoveAsLast()
	{
		// single root
		$array=NestedSet::model()->findAll();

		$NestedSet=NestedSet::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$target=NestedSet::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAsLast($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		$NestedSet=NestedSet::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSet);
		$this->assertTrue($NestedSet->moveAsLast($target));
		$this->assertTrue($this->checkTree());

		$this->assertTrue($this->checkArray($array));

		// many roots
		$array=NestedSetWithManyRoots::model()->findAll();

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(3);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$target=NestedSetWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsLast($target));
		$this->assertTrue($this->checkTreeWithManyRoots());

		$this->assertTrue($this->checkArrayWithManyRoots($array));

		$NestedSet=NestedSetWithManyRoots::model()->findByPk(2);
		$this->assertTrue($NestedSet instanceof NestedSetWithManyRoots);
		$this->assertTrue($NestedSet->moveAsLast($target));
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
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSet` WHERE `lft`>=`rgt`;')->queryScalar();
	}

	private function checkTree2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSet` WHERE NOT MOD(`rgt`-`lft`,2);')->queryScalar();
	}

	private function checkTree3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSet` WHERE MOD(`lft`-`level`,2);')->queryScalar();
	}

	private function checkTree4()
	{
		$row=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `NestedSet`;')->queryRow(false);

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
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSetWithManyRoots` WHERE `lft`>=`rgt` GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSetWithManyRoots` WHERE NOT MOD(`rgt`-`lft`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `NestedSetWithManyRoots` WHERE MOD(`lft`-`level`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkTreeWithManyRoots4()
	{
		$rows=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `NestedSetWithManyRoots` GROUP BY `root`;')->queryAll(false);

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