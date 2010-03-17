<?php
/**
 * StatusTest
 */
class StatusTest extends CDbTestCase {
    public $fixtures=array(
        'posts'=>'Post',
    );

	function testGetStatus(){
		$this->setUp();

		$post = Post::model()->findByPk(1);
		$post->setStatus(1);
		$this->assertEquals(1, $post->getStatus());
	}

	function testGetStatusText(){
		$this->setUp();

		$post = Post::model()->findByPk(1);
		$post->setStatus(1);
		$this->assertEquals('published', $post->getStatusText());
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
        $this->assertEquals('draft', $post->getStatusText(FALSE));
    }

    function testModelSave(){
        $this->setUp();
        
        $post = Post::model()->findByPk(1);
        $post->setStatus('archived');
        $post->save();

        $post = Post::model()->findByPk(1);
        $this->assertEquals('archived', $post->getStatusText(FALSE));
    }
}

