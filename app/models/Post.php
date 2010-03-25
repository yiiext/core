<?php
/**
 * Post model
 *
 * @property id
 */
class Post extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function behaviors() {
        // import taggable folder because of EARTaggableBehaviour inheritance
        Yii::import('ext.yiiext.behaviors.model.taggable.*');
        
        return array(
            'tags' => array(
                'class' => 'ETaggableBehaviour',
                //'cacheID' => 'cache'
            ),
            'colors' => array(
                'class' => 'ETaggableBehaviour',
                'tagTable' => 'Color',
                'tagBindingTable' => 'PostColor',
                'tagBindingTableTagId' => 'colorId',
            ),
            'food' => array(
                'class' => 'EARTaggableBehaviour',
                'tagTable' => 'Food',
                'tagModel' => 'Food',
                'tagBindingTable' => 'PostFood',
                'tagBindingTableTagId' => 'foodId',
                'tagTableName' => 'title',
                'tagTableCount' => 'count'
            ),
            'statuses' => array(
                'class' => 'ext.yiiext.behaviors.model.status.EStatusBehavior',
                'statusField' => 'status',
				        //'statuses' => array('draft' => 'draft', 'published' => 'published', 'archived' => 'archived'),
            ),
        );
    }
}
