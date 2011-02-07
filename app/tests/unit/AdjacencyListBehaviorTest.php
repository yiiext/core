<?php
class AdjacencyListBehaviorTest extends CDbTestCase
{
	public $fixtures=array(
		'AdjacencyList',
	);

	public function testGetRoot()
	{
		$root=AdjacencyList::model()->getRoot();
		$this->assertTrue($root instanceof AdjacencyList);
		$this->assertEquals($root->primaryKey,1);
	}

	/**
	* @depends testGetRoot
	*/

	public function testDescendants()
	{
		$root=AdjacencyList::model()->getRoot();
		$descendants=$root->descendants(1)->findAll();
		$this->assertEquals(count($descendants),2);
	}
}