<?php
/**
 * СTaggableBehaviourTest.php
 */
class CTaggableBehaviourTest extends CDbTestCase {
    public $fixtures=array(
        'posts'=>'Post',
    );

    function setUp(){
        parent::setUp();
        Yii::app()->db->createCommand("truncate Tag")->query();
        Yii::app()->db->createCommand("truncate PostTag")->query();
    }

    function testGetTags(){
        $post = new Post();
        $post->setTags("php, yii");
        $this->assertEquals(array('php', 'yii'), $post->getTags());
    }

    /**
     * @depends testGetTags
     */
    function testSetTags(){
        $post = new Post();
        $post->setTags("php,yii , cool tag  ");
        $this->assertEquals(array("php", "yii", "cool tag"), $post->getTags());

        $post->setTags("php");
        $this->assertEquals(array("php"), $post->getTags());

        $post->setTags(array("php", "yii"));
        $this->assertEquals(array("php", "yii"), $post->getTags());
    }

    /**
     * @depends testGetTags
     */
    function testAddTags(){
        $post = new Post();
        $post->setTags("php");
        $post->addTags("  yii, cool tag");
        $this->assertEquals(array("php", "yii", "cool tag"), $post->getTags());
    }

    /**
     * @depends testGetTags
     */
    function testRemoveTags(){
        $post = new Post();
        $post->setTags("php, yii");
        $post->removeTags("yii");
        $this->assertEquals(array("php"), $post->getTags());
    }

    function testRemoveAllTags(){
        $post = new Post();
        $post->setTags("php, yii");
        $post->removeAllTags();

        $this->assertEquals(array(), $post->getTags());        
    }

    /**
     * @depends testGetTags
     * @depends testSetTags
     */
    function testAfterSaveAndAfterFind(){
        $post = new Post();
        $post->setTags("php, yii");
        $post->save();
        $id = $post->id;

        $post = Post::model()->findByPk($id);
        $tagsArray = $post->getTags();
        $this->assertTrue(in_array('php', $tagsArray));
        $this->assertTrue(in_array('yii', $tagsArray));
    }

    /**
     * @depends testSetTags
     * @depends testAfterSaveAndAfterFind
     */
    function testGetAllTagsWithModelsCount(){
        $this->prepareTags();       

        $tagsWithModelsCount = Post::model()->getAllTagsWithModelsCount();

        $this->assertTrue(in_array(array(
            'name' => 'yii',
            'count' => 2
        ), $tagsWithModelsCount));

        $this->assertTrue(in_array(array(
            'name' => 'mysql',
            'count' => 1
        ), $tagsWithModelsCount));

        $this->assertTrue(in_array(array(
            'name' => 'php',
            'count' => 1
        ), $tagsWithModelsCount));
    }

    /**
     * @return testSetTags
     * @depends testAfterSaveAndAfterFind
     */
    function testGetCountByTags(){
        $this->prepareTags();

        $count = Post::model()->getCountByTags("yii");
        $this->assertEquals(2, $count);

        $count = Post::model()->getCountByTags(" php   ,   yii ");
        $this->assertEquals(1, $count);

        $count = Post::model()->getCountByTags("don't have such a tag");
        $this->assertEquals(0, $count);
    }

    /**
     * @return testSetTags
     * @depends testAfterSaveAndAfterFind
     */
    function testFindAllByTags(){
        $this->prepareTags();

        $posts = Post::model()->findAllByTags("yii");
        $this->assertEquals(2, count($posts));

        $posts = Post::model()->findAllByTags(" php   ,   yii ");        
        $this->assertEquals(1, count($posts));
    }

    /**
     * @return testSetTags
     * @depends testAfterSaveAndAfterFind
     */
    function testGetAllTags(){
        $this->prepareTags();

        $tags = Post::model()->getAllTags();

        $this->assertTrue(in_array('php', $tags));
        $this->assertTrue(in_array('yii', $tags));
        $this->assertTrue(in_array('mysql', $tags));
    }

    private function prepareTags(){
        $this->setUp();

        $post = Post::model()->findByPk(1);
        $post->setTags("yii, mysql, php");
        $post->save();

        $post = Post::model()->findByPk(2);
        $post->setTags("yii");
        $post->save();        
    }

    /**
     * @todo: verbose checks
     */
    function testAfterDelete(){
        $this->prepareTags();
        $post = Post::model()->findByPk(1);
        $post->delete();       
    }

    function testToString(){
        $this->setUp();

        $post = Post::model()->findByPk(1);
        $post->setTags("yii, mysql, php");
        $this->assertEquals("yii, mysql, php", (string)$post->tags);
    }

    //---new syntax---
    function testNewTaggedWith(){
        $posts = Post::model()->taggedWith('php, yii')->findAll();

        $posts = Post::model()->taggedWith(array('php', 'yii'))->findAll();

        $postCount = Post::model()->taggedWith('php')->count();
    }
}
