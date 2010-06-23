<?php
class ENestedSetBehaviorTest extends CDbTestCase
{
	public $fixtures=array(
		'categories'=>'Category',
	);

	public function testDescendants()
	{
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
	}

	public function testChildren()
	{
		$category=Category::model()->findByPk(1);
		$this->assertTrue($category instanceof Category);
		$children=$category->children()->findAll();
		$this->assertEquals(count($children),2);
		foreach($children as $child)
			$this->assertTrue($child instanceof Category);
		$this->assertEquals($children[0]->primaryKey,2);
		$this->assertEquals($children[1]->primaryKey,5);
	}

	public function testAncestors()
	{
		$category=Category::model()->findByPk(7);
		$this->assertTrue($category instanceof Category);
		$ancestors=$category->ancestors()->findAll();
		$this->assertEquals(count($ancestors),2);
		foreach($ancestors as $ancestor)
			$this->assertTrue($ancestor instanceof Category);
		$this->assertEquals($ancestors[0]->primaryKey,1);
		$this->assertEquals($ancestors[1]->primaryKey,5);
	}

	public function testRoots()
	{
		$roots=Category::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
			$this->assertTrue($root instanceof Category);
		$this->assertEquals($roots[0]->primaryKey,1);
		$this->assertEquals($roots[1]->primaryKey,8);
	}

	public function testParent()
	{
		$category=Category::model()->findByPk(4);
		$this->assertTrue($category instanceof Category);
		$parent=$category->parent();
		$this->assertTrue($parent instanceof Category);
		$this->assertEquals($parent->primaryKey,2);
	}

	public function testGetPrevSibling()
	{
		$category=Category::model()->findByPk(7);
		$this->assertTrue($category instanceof Category);
		$sibling=$category->getPrevSibling();
		$this->assertTrue($sibling instanceof Category);
		$this->assertEquals($sibling->primaryKey,6);
		$sibling=$sibling->getPrevSibling();
		$this->assertNull($sibling);
	}

	public function testGetNextSibling()
	{
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$sibling=$category->getNextSibling();
		$this->assertTrue($sibling instanceof Category);
		$this->assertEquals($sibling->primaryKey,7);
		$sibling=$sibling->getNextSibling();
		$this->assertNull($sibling);
	}

	public function testIsDescendantOf()
	{
		$category=Category::model()->findByPk(1);
		$this->assertTrue($category instanceof Category);
		$descendants=$category->descendants()->findAll();
		$this->assertEquals(count($descendants),6);
		foreach($descendants as $descendant)
		{
			$this->assertTrue($descendant instanceof Category);
			$this->assertTrue($descendant->isDescendantOf($category));
		}
		$descendant=Category::model()->findByPk(4);
		$this->assertTrue($descendant instanceof Category);
		$this->assertFalse($category->isDescendantOf($descendant));
	}

	public function testIsRoot()
	{
		$roots=Category::model()->roots()->findAll();
		$this->assertEquals(count($roots),2);
		foreach($roots as $root)
		{
			$this->assertTrue($root instanceof Category);
			$this->assertTrue($root->isRoot());
		}
		$notRoot=Category::model()->findByPk(4);
		$this->assertTrue($notRoot instanceof Category);
		$this->assertFalse($notRoot->isRoot());
	}

	public function testIsLeaf()
	{
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
	}

	public function testSave()
	{
		$category=new Category;
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
		$category=Category::model()->findByPk(4);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->tree->delete());
		$this->assertTrue($this->checkTree());
		$category=Category::model()->findByPk(9);
		$this->assertTrue($category instanceof Category);
		$this->assertTrue($category->tree->delete());
		$this->assertTrue($this->checkTree());
	}

	public function testInsertBefore()
	{
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$category=new Category;
		$this->assertFalse($category->insertBefore($target));
		$category->name='test';
		$this->assertTrue($category->insertBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testInsertAfter()
	{
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$category=new Category;
		$this->assertFalse($category->insertAfter($target));
		$category->name='test';
		$this->assertTrue($category->insertAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testPrependTo()
	{
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$category=new Category;
		$this->assertFalse($category->prependTo($target));
		$category->name='test';
		$this->assertTrue($category->prependTo($target));
		$this->assertTrue($this->checkTree());
	}

	public function testAppendTo()
	{
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$category=new Category;
		$this->assertFalse($category->prependTo($target));
		$category->name='test';
		$this->assertTrue($category->prependTo($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore()
	{
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore2()
	{
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore3()
	{
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(9);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveBefore4()
	{
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(9);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter()
	{
		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter2()
	{
		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter3()
	{
		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(12);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAfter4()
	{
		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(12);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst()
	{
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst2()
	{
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(2);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst3()
	{
		$category=Category::model()->findByPk(6);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(9);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsFirst4()
	{
		$category=Category::model()->findByPk(5);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(9);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveBefore($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast()
	{
		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast2()
	{
		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(5);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast3()
	{
		$category=Category::model()->findByPk(3);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(12);
		$this->assertTrue($target instanceof Category);
		$this->assertTrue($category->moveAfter($target));
		$this->assertTrue($this->checkTree());
	}

	public function testMoveAsLast4()
	{
		$category=Category::model()->findByPk(2);
		$this->assertTrue($category instanceof Category);
		$target=Category::model()->findByPk(12);
		$this->assertTrue($target instanceof Category);
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
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `category` WHERE `lft`>=`rgt` GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity2()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `category` WHERE NOT MOD(`rgt`-`lft`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity3()
	{
		return !Yii::app()->db->createCommand('SELECT COUNT(`id`) FROM `category` WHERE MOD(`lft`-`level`,2) GROUP BY `root`;')->query()->getRowCount();
	}

	private function checkIntegrity4()
	{
		$result=true;
		$rows=Yii::app()->db->createCommand('SELECT MIN(`lft`),MAX(`rgt`),COUNT(`id`) FROM `category` GROUP BY `root`;')->queryAll(false);
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