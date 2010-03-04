<?php
/**
 * Contact model
 */
class Contact extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function behaviors() {
        return array(
            'attr' => array(
                'class' => 'ext.yiiext.behaviors.model.eav.CEavBehavior',
                'tableName' => 'contactattr',
                'safeAttributes' => array('phone', 'skype'),
                //'cacheId' => 'cache',
                'preload' => FALSE,
            ),
        );
    }
}
