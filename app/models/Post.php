<?php
class Post extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function behaviors() {
        return array(
            'taggable' => array(
                'class' => 'ext.CTaggableBehaviour.CTaggableBehaviour',    
            )
        );
    }
}
