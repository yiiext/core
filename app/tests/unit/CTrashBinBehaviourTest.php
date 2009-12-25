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

        Fruit::model()->disableBehavior('trash');
        $fruit = Fruit::model()->findByPk(1);
        $this->assertTrue($fruit->isRemoved());
        Fruit::model()->enableBehavior('trash');
    }

    function testRestore(){
        $this->setUp();

        $fruit = Fruit::model()->findByPk(1);
        $fruit->remove()->save();

        Fruit::model()->disableBehavior('trash');

        $fruit = Fruit::model()->findByPk(1);
        $fruit->restore()->save();

        // Включаем снова поведение.
        Fruit::model()->enableBehavior('trash');

        $fruits = Fruit::model()->findAll();
        $this->assertEquals(3, count($fruits));    
    }
}
