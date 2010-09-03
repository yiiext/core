<?php
/**
 * TrashBinTest
 */
class TrashBinTest extends CDbTestCase
{
	public $fixtures=array(
		'fruits'=>'Fruit',
	);

	function testRemove()
	{
		$this->setUp();

		$fruit=Fruit::model()->findByPk(1);
		$fruit->remove()->save();

		$fruits=Fruit::model()->findAll();
		$this->assertEquals(2,count($fruits));
	}
	function testRestore()
	{
		$this->setUp();

		$fruit=Fruit::model()->findByPk(1);
		$fruit->remove()->save();

		$fruit=Fruit::model()->withRemoved()->findByPk(1);
		$fruit->restore()->save();

		$fruits=Fruit::model()->findAll();
		$this->assertEquals(3, count($fruits));
	}
	function testWithRemoved()
	{
		$this->setUp();

		// Load all models excepts removed.
		$fruits=Fruit::model()->findAll();
		$this->assertEquals(3,count($fruits));

		// Remove model to trash.
		$fruit=Fruit::model()->findByPk(1);
		$fruit->remove()->save();

		// Load all models include removed.
		$fruits=Fruit::model()->withRemoved()->findAll();
		$this->assertEquals(3,count($fruits));

		// Load all models excepts removed.
		$fruits=Fruit::model()->findAll();
		$this->assertEquals(2,count($fruits));

		// Count models include removed.
		$fruitsCount=Fruit::model()->withRemoved()->count();
		$this->assertEquals(3,$fruitsCount);

		// Count models excepts removed.
		$fruitsCount=Fruit::model()->withRemoved()->filterRemoved()->count();
		$this->assertEquals(2,$fruitsCount);
	}
	function testIsRemoved()
	{
		$this->setUp();

		// Remove model to trash.
		$fruit=Fruit::model()->findByPk(1);
		$fruit->remove()->save();

		$fruit=Fruit::model()->withRemoved()->findByPk(1);
		$this->assertTrue($fruit->getIsRemoved());
		$this->assertTrue($fruit->isRemoved);
	}
}
