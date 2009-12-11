<?php
/**
 * CEavBehavior class file.
 *
 * Entity-Attribute-Value behavior (EAV)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yii-slavco-dev/wiki/CEavBehavior
 */

/**
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @package yii-slavco-dev
 * @version 1.3
 *
 * @todo Lazy loading
 * @todo Add translate for messages
 * @todo Add caching
 */

 /**
  * Changelog
  * 1.3
  * [-] Delete method readEavAttributes
  * [+] Search model by eav-attributes
  * [+] All queries in DB will generated CDbCriteria by
  * [*] Method saveEavAttributes will be change in saveEavAttribute for save each attribute
  * [*] Change method getModelTableFk now return value of primary key, for name call getModelTableFkField
  *
  * 1.2
  * [-] AR for each eav-attribute
  *
  * 1.1
  * [+] First version
  */

class CEavBehavior extends CActiveRecordBehavior {
    /**
     * @var string The name of the table where data stored.
     * Required to set on init behavior. No defaults.
     */
    public $tableName = '';

    /**
     * @var string The name of the column for entity store.
     * Defaults to 'entity'.
     */
    public $entityField = 'entity';

    /**
     * @var string The name of the column for attribute store.
     * Defaults to 'attribute'.
     */
    public $attributeField = 'attribute';

    /**
     * @var string The name of the column for value store.
     * Defaults to 'value'.
     */
    public $valueField = 'value';

    /**
     * @var string Owner model FK name.
     * Defaults primaryKey from model.
     */
    public $modelTableFk = null;

    /**
     * @var array The array of filtered attributes name.
     * Defaults is empty.
     */
    public $safeAttributes = array();

    /**
     * @var string The prefix for each attribute.
     * Defaults is empty.
     */
    public $attributesPrefix = '';

    private $attributes = array();
    private $attributesForSave = array();

    public function attach($owner) {
        // Check required var tableName
        if (is_string($this->tableName) === FALSE || empty($this->tableName) === TRUE) {
            throw new CException(Yii::t('CEAV', 'Required var "{class}.{property}" not set.',
                array('{class}' => get_class($this), '{property}' => 'tableName')));
        }
        parent::attach($owner);
    }

    private function getModelTableFkField() {
        if (is_string($this->modelTableFk) === TRUE && empty($this->modelTableFk) === FALSE) {
            return trim($this->modelTableFk);
        }
        $modelTableFk = $this->getOwner()->getTableSchema()->primaryKey;
        if (is_string($modelTableFk) === TRUE) {
            return $modelTableFk;
        }
        throw new CException(Yii::t('CEAV', 'Cannot access model table foreign key.', array()));
    }
    private function getModelTableFk() {
        $modelTableFk = $this->getModelTableFkField();
        return $this->getOwner()->$modelTableFk;
    }

    /**
     * Search first model by eav-attributes.
     *
     * @param array Eav-attributes indexed by attribute name
     * @param string Addition condition
     * @param array Params used only if used $condition
     * @return CActiveRecord or NULL
     */
    public function findByEavAttributes($attributes, $condition = '', $params = array()) {
        return $this->getOwner()->find($this->getFindEavAttributeCriteria($attributes, $condition, $params));
    }

    /**
     * Search all models by eav-attributes.
     *
     * @param array Eav-attributes indexed by attribute name
     * @param string Addition condition
     * @param array Params used only if used $condition
     * @return array
     */
    public function findAllByEavAttributes($attributes, $condition = '', $params = array()) {
        return $this->getOwner()->findAll($this->getFindEavAttributeCriteria($attributes, $condition, $params));
    }

    /**
     * Get the attributes values indexed by attributes name.
     * 
     * @param array The attributes names
     * @return array
     */
    public function getEavAttributes($attributes = array()) {
        // if param $attributes is array return only required attributes
        if (is_array($attributes) === TRUE && count($attributes) > 0) {
            $values = array();
            foreach ($attributes as $attribute) {
                if ($this->checkEavAttribute($attribute) === TRUE) {
                    $values[$attribute] = $this->getEavAttribute($attribute);
                }
            }
            return $values;
        }
        // is param $attributes is empty return all loading attributes
        else {
            return $this->attributes;
        }
    }

    /**
     * Get the entity attribute value.
     * 
     * @param string The attribute name
     * @return string The attribute value
     */
    public function getEavAttribute($attribute) {
        if ($this->checkEavAttribute($attribute) === TRUE) {
            return isset($this->attributes[$attribute]) === TRUE ? $this->attributes[$attribute] : NULL;
        }
        return FALSE;
    }

    /**
     * Set attribute value.
     *
     * @param string The attribute name
     * @param mixed The attribute value
     */
    public function setEavAttribute($attribute, $value) {
        if ($this->checkEavAttribute($attribute) === TRUE) {
            $this->attributes[$attribute] = $value;
            // remember changed attribute
            if (array_key_exists($this->attributesPrefix . $attribute, $this->attributesForSave) === FALSE) {
                $this->attributesForSave[$this->attributesPrefix . $attribute] = $value;
            }
        }
    }

    /**
     * Check is valid attribute name.
     *
     * @param string
     * @return bool
     */
    public function checkEavAttribute($attribute) {
        // attribute name need be string
        if(is_string($attribute) === FALSE) {
            return FALSE;
        }
        // if set safeAttributes, check is it
        if (is_array($this->safeAttributes) === TRUE && count($this->safeAttributes) > 0
            && in_array($attribute, $this->safeAttributes) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Load and parse attributes. Can be load only attributes set in first param.
     * 
     * @param mixed The attributes that searched. Attributes array or once attribute name.
     * @return CEavBehavior
     */
    protected function loadEavAttributes($attributes = array()) {
        is_array($attributes) === TRUE OR $attributes = array($attributes);

        $validAttributes = array();
        foreach ($attributes as $i => $attribute) {
            if ($this->checkEavAttribute($attribute) === TRUE) {
                $validAttributes[] = $this->attributesPrefix . $attribute;
            }
        }


        $dataReader = $this->getOwner()
            ->getCommandBuilder()
            ->createFindCommand($this->tableName, $this->getLoadEavAttributesCriteria($validAttributes))
            ->query();

        $prefixLength = strlen($this->attributesPrefix);
        foreach($dataReader as $row) {
            $attributeLabel = substr($row[$this->attributeField], $prefixLength);
            if (isset($this->attributes[$attributeLabel]) === TRUE) {
                is_array($this->attributes[$attributeLabel]) === TRUE
                    ? $this->attributes[$attributeLabel][] = $row[$this->valueField]
                    : $this->attributes[$attributeLabel] = array($this->attributes[$attributeLabel], $row[$this->valueField]);
            }
            else {
                $this->attributes[$attributeLabel] = $row[$this->valueField];
            }
        }
        return $this;
    }

    /**
     * Save eav-attribute.
     *
     * @return bool
     */
    protected function createSaveEavAttributeCommand($attribute, $values) {
        // if null delete attribute from db
        if (is_null($values) === TRUE) return FALSE;
        // make array of values for convenience
        if (is_array($values) === FALSE) $values = array($values);
        // each value insert in db
        foreach ($values as $value) {
            $data = array(
                $this->entityField => $this->getModelTableFk(),
                $this->attributeField => $attribute,
                $this->valueField => $value,
            );
        }

        return $this->getOwner()
            ->getCommandBuilder()
            ->createInsertCommand($this->tableName, $data);
    }

    /**
     * Create criteria for load eav-attributes.
     * This method may be overloaded if you use other schema, not one table for eav-attributes.
     *
     * @param mixed The attributes that searched. Attributes array or once attribute name.
     * @return CDbCriteria
     */
    protected function getLoadEavAttributesCriteria($attributes = array()) {
        $criteria = new CDbCriteria;
        $criteria->condition = $this->entityField . ' = :entity';
        $criteria->params = array(':entity' => $this->getModelTableFk());
        if (is_array($attributes) === TRUE && count($attributes) > 0) {
            $criteria->addInCondition($this->attributeField, $attributes);
        }

        return $criteria;
    }

    /**
     * Create criteria for find models by eav-attribute.
     *
     * @param array Eav-attributes indexed by attribute name
     * @param string Addition condition
     * @param array Params used only if used $condition
     * @return CDbCriteria
     */
    protected function getFindEavAttributeCriteria($attributes, $condition = '', $params = array()) {
        if (is_array($attributes) === FALSE) {
            throw new CException(Yii::t('CEAV', 'For search required array of attributes and values.'));
        }

        $criteria = new CDbCriteria;
        $criteria->condition = $condition;
        $criteria->params = $params;
        $criteria->join = 'LEFT JOIN ' . $this->tableName . ' ON ' . $this->getModelTableFkField() . ' = ' . $this->tableName . '.' . $this->entityField;
        $criteria->group = $this->getModelTableFkField();

        foreach ($attributes as $attribute => $values) {
            $attributeCondition = $this->tableName . '.' . $this->attributeField . ' = "' . $this->attributesPrefix . $attribute . '"';
            $valuesCondition = array();
            if (is_array($values) === FALSE) {
                $values = array($values);
            }
            foreach ($values as $value) {
                $valuesCondition[] = $this->tableName . '.' . $this->valueField . ' LIKE "%' . $value . '%"';
            }
            $criteria->addCondition(array(
                    $attributeCondition,
                    implode(' OR ', $valuesCondition),
                ), 'OR');
        }
        return $criteria;
    }

    /**
     * Create criteria for delete all eav-attributes.
     *
     * @return CdbCriteria
     */
    protected function getDeleteEavAttributesCriteria($attributes = array()) {
        $criteria = new CDbCriteria;
        $criteria->condition = $this->entityField . ' = :entity';
        $criteria->params = array(':entity' => $this->getModelTableFk());
        if (is_array($attributes) === TRUE) {
            $criteria->addInCondition($this->attributeField, $attributes);
        }

        return $criteria;
    }

    /**
     * Load attributes after finding model if preload set.
     *
     * @param CModelEvent
     */
    public function afterFind($event) {
        $attributes = array();
        if (is_array($this->safeAttributes) === TRUE && count($this->safeAttributes) > 0) {
            $attributes = $this->safeAttributes;
        }
        $this->loadEavAttributes($attributes);
        parent::afterFind($event);
    }

    /**
     * Save attributes after save model.
     *
     * @param CModelEvent
     */
    public function afterSave($event) {
        if (count($this->attributesForSave) > 0) {
            // delete old values of changed attributes
            $this->getOwner()
                ->getCommandBuilder()
                ->createDeleteCommand($this->tableName, $this->getDeleteEavAttributesCriteria(array_keys($this->attributesForSave)))
                ->execute();

            foreach ($this->attributesForSave as $attribute => $values) {
                $this->createSaveEavAttributeCommand($attribute, $values)->execute();
            }
            $this->attributesForSave = array();
        }
        parent::afterSave($event);
    }

    /**
     * Delete attributes after delete model.
     *
     * @param CModelEvent
     */
    public function afterDelete($event) {
        // delete all attributes from db
        $this->getOwner()
            ->getCommandBuilder()
            ->createDeleteCommand($this->tableName, $this->getDeleteAllEavAttributesCriteria())
            ->execute();

        $this->attributes = array();
        parent::afterDelete($event);
    }
}
