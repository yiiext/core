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
                'class' => 'ext.CTaggableBehaviour.CTaggableBehaviour',
                'cacheID' => 'cache'
            ),
            'colors' => array(
                'class' => 'ext.CTaggableBehaviour.CTaggableBehaviour',
                'tagTable' => 'Color',
                'tagBindingTable' => 'PostColor',
                'tagBindingTableTagId' => 'colorId',
            ),
            'statuses' => array(
                'class' => 'ext.CStatusBehavior.CStatusBehavior',
                'statusField' => 'status',
            ),
        );
    }
}
