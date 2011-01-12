<?php
class ENestedSetBehaviorManyRootsModeTest extends CDbTestCase
{
	public $fixtures=array(
		'categories'=>'CategoryWithManyRoots',
	);

	public function testDescendants()
	{
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
		$roots=CategoryWithManyRoots::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
			$this->assertTrue($root instanceof CategoryWithManyRoots);
		$this->assertEquals($roots[0]->primaryKey,1);
		$this->assertEquals($roots[1]->primaryKey,8);
	}

	public function testGetParent()
	{
		$category=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$parent=$category->getParent();
		$this->assertTrue($parent instanceof CategoryWithManyRoots);
		$this->assertEquals($parent->primaryKey,2);
	}

	public function testGetPrevSibling()
	{
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
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$sibling=$category->getNextSibling();
		$this->assertTrue($sibling instanceof CategoryWithManyRoots);
		$this->assertEquals($sibling->primaryKey,7);
		$sibling=$sibling->getNextSibling();
		$this->assertNull($sibling);
	}

	public function testIsDescendantOf()
	{
		$category=CategoryWithManyRoots::model()->findByPk(1);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof CategoryWithManyRoots);
			$this->assertTrue($descendant->isDescendantOf($category));
		}
		$descendant=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($descendant instanceof CategoryWithManyRoots);
		$this->assertFalse($category->isDescendantOf($descendant));
	}

	public function testIsRoot()
	{
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

	public function testSave()
	{
		$category=new CategoryWithManyRoots;
		$this->assertFalse($category->tree->save());
		$category->name='test';
		$this->assertTrue($category->tree->save());
		$this->assertEquals($category->root,$category->primaryKey);
		$this->assertEquals($category->lft,1);
		$this->assertEquals($category->rgt,2);
		$this->assertEquals($category->level,1);
	}

	public function testDelete()
	{
		$category=CategoryWithManyRoots::model()->findByPk(4);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->tree->delete());
		$this->assertTrue($this->checkTree());
		$category=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$this->assertTrue($category->tree->delete());
		$this->assertTrue($this->checkTree());
	}

	public function testInsertBefore()
	{
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category=new Category;
		$this->assertFalse($category->insertBefore($target));
		$category->name='test';
		$this->assertTrue($category->insertBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testInsertAfter()
	{
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category=new Category;
		$this->assertFalse($category->insertAfter($target));
		$category->name='test';
		$this->assertTrue($category->insertAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testPrependTo()
	{
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category=new Category;
		$this->assertFalse($category->prependTo($target));
		$category->name='test';
		$this->assertTrue($category->prependTo($target));
		$this->assertTrue($this->checkTree());
	}

	public function testAppendTo()
	{
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$category=new Category;
		$this->assertFalse($category->appendTo($target));
		$category->name='test';
		$this->assertTrue($category->appendTo($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore()
	{
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore2()
	{
		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore3()
	{
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore4()
	{
		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter()
	{
		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter2()
	{
		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter3()
	{
		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter4()
	{
		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst()
	{
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst2()
	{
		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst3()
	{
		$category=CategoryWithManyRoots::model()->findByPk(6);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst4()
	{
		$category=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(9);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast()
	{
		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast2()
	{
		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(5);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast3()
	{
		$category=CategoryWithManyRoots::model()->findByPk(3);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast4()
	{
		$category=CategoryWithManyRoots::model()->findByPk(2);
		$this->assertTrue($category instanceof CategoryWithManyRoots);
		$target=CategoryWithManyRoots::model()->findByPk(12);
		$this->assertTrue($target instanceof CategoryWithManyRoots);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	private function checkTree()
	{
		return $this->checkIntegrity1()
			&& $this->checkIntegrity2()
			&& $this->checkIntegrity3()
			&& $this->checkIntegrity4();
	}

	private function checkIntegrity1()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE `lft`>=`rgt` GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE NOT MOD(`rgt`-`lft`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `CategoryWithManyRoots` WHERE MOD(`lft`-`level`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity4()
	{
		$result=true;
		$rows=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `CategoryWithManyRoots` GROUP BY `root`;')->queryAll(false);
		foreach($rows as $row)
		{
			if($row[0]!=1 || $row[1]!=$row[2]*2)
			{
				$result=false;
				break;
			}
		}
		return $result;
	}
}