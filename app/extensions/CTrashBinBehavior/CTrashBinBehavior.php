<?php
/**
 * CTrashBinBehavior class file.
 *
 * Trash bin behavior for models
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yii-slavco-dev/wiki/CTrashBinBehavior
 */

/**
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @package yii-slavco-dev
 * @version 1.1
 *
 * @todo
 */

 /**
  * 1.1
  * [+] First version
  */

class CTrashBinBehavior extends CActiveRecordBehavior {
    /**
     * @var string The name of the table where data stored.
     * Required to set on init behavior. No defaults.
     */
    public $trashFlagField = NULL;
    /**
     * @var mixed The value to set for removed model.
     * Default is 1.
     */
    public $removedFlag = '1';
    /**
     * @var mixed The value to set for restored model.
     * Default is 0.
     */
    public $restoredFlag = '0';

    public function attach($owner) {
        // Check required var trashFlagField
        if (is_string($this->trashFlagField) === FALSE || empty($this->trashFlagField) === TRUE) {
            throw new CException(Yii::t('CEAV', 'Required var "{class}.{property}" not set.',
                array('{class}' => get_class($this), '{property}' => 'trashFlagField')));
        }
        parent::attach($owner);
    }

    /**
     * Remove model in trash bin.
     *
     * @return CActiveRecord
     */
    public function remove() {
        $trashFlagField = $this->trashFlagField;
        $this->getOwner()->$trashFlagField = $this->removedFlag;
        return $this->getOwner();
    }
    
    /**
     * Restore model from trach bin.
     *
     * @return CActiveRecord
     */
    public function restore() {
        $trashFlagField = $this->trashFlagField;
        $this->getOwner()->$trashFlagField = $this->restoredFlag;
        return $this->getOwner();
    }

    /**
     * Check if model is removd in trash bin.
     *
     * @return bool
     */
    public function isRemoved() {
        $trashFlagField = $this->trashFlagField;
        return $this->getOwner()->$trashFlagField == $this->removedFlag ? TRUE : FALSE;
    }

    /**
     * Add condition before find, for except models from trash bin.
     *
     * @param CEvent
     */
    public function beforeFind($event) {
        if ($this->getEnabled() === TRUE) {
            $criteria = $this->getOwner()->getDbCriteria();
            $criteria->addCondition($this->trashFlagField . ' != "' . $this->removedFlag . '"');
        }
        parent::beforeFind($event);
    }
    
}
