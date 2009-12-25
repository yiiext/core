<?php
/**
 * CStatusBehaviourTest
 */
class CStatusBehaviourTest extends CDbTestCase {
    public $fixtures=array(
        'posts'=>'Post',
    );

    function testSetStatuses(){
        
    }

    function testGetStatuses(){
        
    }

    function testGetStatusGroup(){
        
    }

    function testGetStatusText(){
        $this->setUp();
        
        $post = Post::model()->findByPk(1);
        $post->setStatus('draft');
        $this->assertEquals('draft', $post->getStatusText());        
    }

    function testGetTranslatedStatusText(){
        $this->setUp();

        $post = Post::model()->findByPk(1);
        $post->setStatus('draft');
        $this->assertEquals('черновик', $post->getStatusText(true));
    }

    
    function testSetStatus(){
        $post = Post::model()->findByPk(1);
        $post->setStatus('draft');
        $this->assertEquals('draft', $post->getStatusText());
    }

    /**
     * @expectedException Exception
     */
    function testSetStatusException(){
        $this->setUp();

        $post = Post::model()->findByPk(1);        
        $post->setStatus('wrong status');
    }

    function testSaveStatus(){
        $this->setUp();

        $post = Post::model()->findByPk(1);
        $post->setStatus('draft')->saveStatus();

        $post = Post::model()->findByPk(1);
        $this->assertEquals('draft', $post->getStatusText());
    }

    function testAfterFind(){
        
    }

    function testAfterSave(){
        $this->setUp();
        
        $post = Post::model()->findByPk(1);
        $post->setStatus('draft');
        $post->save();

        $post = Post::model()->findByPk(1);
        $this->assertEquals('draft', $post->getStatusText());
    }
}

