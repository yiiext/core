<?php
/**
 * CTrashBinBehaviourTest
 */

class CTrashBinBehaviourTest extends CDbTestCase {
    public $fixtures=array(
        'fruits'=>'Fruit',
    );

    function testRemove(){
        $this->setUp();

        $fruit = Fruit::model()->findByPk(1);
        $fruit->remove()->save();

        $fruits = Fruit::model()->findAll();
        $this->assertEquals(2, count($fruits));
    }

    function testIsRemoved(){
        $this->setUp();

        $fruit = Fruit::model()->findByPk(1);
        $fruit->remove()->save();

        $fruit = Fruit::model()->withRemoved()->findByPk(1);
        $this->assertTrue($fruit->isRemoved());
    }

    function testRestore(){
        $this->setUp();

        $fruit = Fruit::model()->findByPk(1);
        $fruit->remove()->save();

        $fruit = Fruit::model()->withRemoved()->findByPk(1);
        $fruit->restore()->save();

        $fruits = Fruit::model()->findAll();
        $this->assertEquals(3, count($fruits));    
    }

    /**
     * Will fail until
     * http://code.google.com/p/yii/issues/detail?id=764
     * is fixed
     */
    function testWithRemoved(){
        $this->setUp();

        $fruit = Fruit::model()->findByPk(1);
        $fruit->remove()->save();
        
        $fruits = Fruit::model()->findAll();
        $this->assertEquals(2, count($fruits));

        $fruits = Fruit::model()->withRemoved()->findAll();
        $this->assertEquals(3, count($fruits));

        $fruits = Fruit::model()->findAll();
        $this->assertEquals(2, count($fruits));

        $fruitsCount = Fruit::model()->count();
        $this->assertEquals(2, $fruitsCount);

        $fruitsCount = Fruit::model()->withRemoved()->count();
        $this->assertEquals(3, $fruitsCount);
    }
}
