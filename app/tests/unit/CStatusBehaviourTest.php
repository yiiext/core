<?php
/**
 * CStatusBehaviourTest
 */
class CStatusBehaviourTest extends CDbTestCase {   
    function testSetStatuses(){
        
    }

    function testGetStatuses(){
        
    }

    function testGetStatusGroup(){
        
    }

    function testGetStatusText(){
        
    }

    function testSetStatus(){

    }

    function testSaveStatus(){
        $this->setUp();

        $post = Post::model()->findByPk(1);
        $post->setStatus('draft')->saveStatus();

        $post = Post::model()->findByPk(1);
        $this->assertEquals('draft', $post->getStatus());
    }

    function testAfterFind(){
        
    }

    function testAfterSave(){
        $this->setUp();
        
        $post = Post::model()->findByPk(1);
        $post->setStatus('draft');
        $post->save();

        $post = Post::model()->findByPk(1);
        $this->assertEquals('draft', $post->getStatus());
    }
}

