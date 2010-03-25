<?php
/**
 * Food model
 *
 * @property id
 */
class Food extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'framework.zii.behaviors.CTimestampBehavior',
            ),
        );
    }*/
}
