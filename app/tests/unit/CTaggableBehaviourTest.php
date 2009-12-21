<?php
/**
 * СTaggableBehaviourTest.php
 */

class CTaggableBehaviourTest extends CDbTestCase {
    public $fixtures=array(
        'posts'=>'Post',
    );

    function testGetTagsArray(){
        $post = new Post();
        $post->setTags("php, yii");
        $this->assertEquals(array('php', 'yii'), $post->getTagsArray());
    }

    function testGetTags(){
        $post = new Post();
        $post->setTags("php,yii , cool tag  ");
        $this->assertEquals("php, yii, cool tag", $post->getTags());
    }

    /**
     * @depends testGetTagsArray
     */
    function testSetTags(){
        $post = new Post();
        $post->setTags("php,yii , cool tag  ");
        $this->assertEquals(array("php", "yii", "cool tag"), $post->getTagsArray());

        $post->setTags("php");
        $this->assertEquals(array("php"), $post->getTagsArray());
    }

    /**
     * @depends testGetTagsArray
     */
    function testAddTags(){
        $post = new Post();
        $post->setTags("php");
        $post->addTags("  yii, cool tag");
        $this->assertEquals(array("php", "yii", "cool tag"), $post->getTagsArray());        
    }

    /**
     * @depends testGetTagsArray
     */
    function testRemoveTags(){
        $post = new Post();
        $post->setTags("php, yii");
        $post->removeTags("yii");
        $this->assertEquals(array("php"), $post->getTagsArray());
    }

    /**
     * @depends testGetTagsArray
     * @depends testSetTags
     */
    function testAfterSaveAndAfterFind(){
        $post = new Post();
        $post->setTags("php, yii");
        $post->save();
        $id = $post->id;

        $post = Post::model()->findByPk($id);
        $tagsArray = $post->getTagsArray();
        $this->assertTrue(in_array('php', $tagsArray));
        $this->assertTrue(in_array('yii', $tagsArray));
    }

    function testGetAllTagsWithModelsCount(){
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

    function testGetCountByTags(){
        $count = Post::model()->getCountByTags("yii");
        $this->assertEquals(2, $count);

        $count = Post::model()->getCountByTags(" php   ,   yii ");
        $this->assertEquals(1, $count);        
    }

    function testFindAllByTags(){
        $posts = Post::model()->findAllByTags("yii");
        $this->assertEquals(2, count($posts));

        $posts = Post::model()->findAllByTags(" php   ,   yii ");        
        $this->assertEquals(1, count($posts));
    }
}
