<?php
class Fruit extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function behaviors() {
        return array(
            'trash' => array(
                'class' => 'ext.CTrashBinBehavior.CTrashBinBehavior',
                'trashFlagField' => 'deleted'                
            ),
        );
    }
}