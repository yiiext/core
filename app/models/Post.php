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
        return array(
            'tags' => array(
                'class' => 'ext.yiiext.behaviors.model.taggable.ETaggableBehaviour',
                'cacheID' => 'cache'
            ),
            'colors' => array(
                'class' => 'ext.yiiext.behaviors.model.taggable.ETaggableBehaviour',
                'tagTable' => 'Color',
                'tagBindingTable' => 'PostColor',
                'tagBindingTableTagId' => 'colorId',
            ),
            'statuses' => array(
                'class' => 'ext.yiiext.behaviors.model.status.EStatusBehavior',
                'statusField' => 'status',
            ),
        );
    }
}
