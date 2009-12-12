<?php
/**
 * CEavBehavior class file.
 *
 * Entity-Attribute-Value behavior.
 * Allows model to work with custom fields on the fly (EAV pattern).
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yii-slavco-dev/wiki/CEavBehavior
 *
 * @version 1.4
 *
 * @todo Lazy loading
 * @todo Add translate for messages
 * @todo Add caching
 */
class CEavBehavior extends CActiveRecordBehavior {
    /**
     * @var string name of the table where data is stored.
     * Required to be set on init behavior. No defaults.
     */
    public $tableName = '';

    /**
     * @var string name of the column to store entity name
     * Default is 'entity'.
     */
    public $entityField = 'entity';

    /**
     * @var string name of the column to store attribute
     * Default is 'attribute'.
     */
    public $attributeField = 'attribute';

    /**
     * @var string name of the column to store value
     * Default is 'value'.
     */
    public $valueField = 'value';

    /**
     * @var string Owner model FK name
     * Default is model's primaryKey
     */
    public $modelTableFk = null;

    /**
     * @var array array of filtered attribute names
     * Empty by default
     */
    public $safeAttributes = array();

    /**
     * @var string prefix for each attribute
     * Empty by default
     */
    public $attributesPrefix = '';

    private $attributes = array();
    private $attributesForSave = array();

    public function attach($owner) {
        // tableName is required
        if (!is_string($this->tableName) || empty($this->tableName)) {
            throw new CException(Yii::t('CEAV', 'Required property "{class}.{property}" is not set.',
                array('{class}' => get_class($this), '{property}' => 'tableName')));
        }
        parent::attach($owner);
    }

    private function getModelTableFkField() {
        if (is_string($this->modelTableFk) && !empty($this->modelTableFk)) {
            return trim($this->modelTableFk);
        }
        $modelTableFk = $this->getOwner()->getTableSchema()->primaryKey;
        if (is_string($modelTableFk)) {
            return $modelTableFk;
        }
        throw new CException(Yii::t('CEAV', 'Cannot get model table foreign key.', array()));
    }
    private function getModelTableFk() {
        $modelTableFk = $this->getModelTableFkField();
        return $this->getOwner()->$modelTableFk;
    }

    /**
     * Find a single model by attribute set
     *
     * @param array attribute set indexed by attribute name
     * @param string additional condition
     * @param array params used only when using $condition
     * @return CActiveRecord|NULL
     */
    public function findByEavAttributes($attributes, $condition = '', $params = array()) {
        return $this->getOwner()->find($this->getFindEavAttributeCriteria($attributes, $condition, $params));
    }

    /**
     * Find all models by attribute set
     *
     * @param array attribute set indexed by attribute name
     * @param string additional condition
     * @param array params used only when using $condition
     * @return array
     */
    public function findAllByEavAttributes($attributes, $condition = '', $params = array()) {
        return $this->getOwner()->findAll($this->getFindEavAttributeCriteria($attributes, $condition, $params));
    }

    /**
     * Get attribute values indexed by attributes name
     * 
     * @param array Array of attribute names to return. Returns all attributes if empty.
     * @return array
     */
    public function getEavAttributes($attributes = array()) {
        // if param $attributes is array, return only attributes that are specified
        if (is_array($attributes) && !empty($attributes)) {
            $values = array();
            foreach ($attributes as $attribute) {
                if ($this->checkEavAttribute($attribute) === TRUE) {
                    $values[$attribute] = $this->getEavAttribute($attribute);
                }
            }
            return $values;
        }
        // if param $attributes is empty, return all attributes
        else {
            return $this->attributes;
        }
    }

    /**
     * Get attribute value
     * 
     * @param string attribute name
     * @return string|false attribute value or null if attribute is not defined
     */
    public function getEavAttribute($attribute) {
        if ($this->checkEavAttribute($attribute)) {
            return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : NULL;
        }
        return null;
    }

    /**
     * Set attribute value
     *
     * @param string attribute name
     * @param mixed attribute value
     */
    public function setEavAttribute($attribute, $value) {
        if ($this->checkEavAttribute($attribute)) {
            $this->attributes[$attribute] = $value;
            // remember changed attribute
            if (array_key_exists($this->attributesPrefix . $attribute, $this->attributesForSave) === FALSE) {
                $this->attributesForSave[$this->attributesPrefix . $attribute] = $value;
            }
        }
    }

    /**
     * Check if attribute name is valid
     *
     * @param string
     * @return bool
     */
    public function checkEavAttribute($attribute) {
        // attribute name should be string
        if(!is_string($attribute)) {
            return FALSE;
        }
        // if safeAttributes set, check it
        if (is_array($this->safeAttributes) && !empty($this->safeAttributes)
            && !in_array($attribute, $this->safeAttributes)) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Load and parse attributes
     * 
     * @param array The attributes that are being searched. Attributes array or once attribute name.
     * @return CEavBehavior
     */
    protected function loadEavAttributes($attributes = array()) {
        is_array($attributes) || $attributes = array($attributes);

        $validAttributes = array();
        foreach ($attributes as $i => $attribute) {
            if ($this->checkEavAttribute($attribute)) {
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
            if (isset($this->attributes[$attributeLabel])) {
                is_array($this->attributes[$attributeLabel])
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
     * Save attribute
     *
     * @return bool
     */
    protected function createSaveEavAttributeCommand($attribute, $values) {
        // if null, delete attribute from db
        if (is_null($values)) return FALSE;
        // create array of values for convenience
        if (!is_array($values)) $values = array($values);
        // save values
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
     * Returns criteria for loading attributes
     * This method should be overloaded if you are using custom DB schema.
     *
     * @param array|string attributes that are searched. Attribute array or single attribute name.
     * @return CDbCriteria
     */
    protected function getLoadEavAttributesCriteria($attributes = array()) {
        $criteria = new CDbCriteria;
        $criteria->condition = $this->entityField . ' = :entity';
        $criteria->params = array(':entity' => $this->getModelTableFk());
        if (is_array($attributes) && !empty($attributes)) {
            $criteria->addInCondition($this->attributeField, $attributes);
        }

        return $criteria;
    }

    /**
     * Returns criteria for finding models by attributes
     *
     * @param array attributes indexed by attribute name
     * @param string Addition condition
     * @param array Params used only when $condition is used
     * @return CDbCriteria
     */
    protected function getFindEavAttributeCriteria($attributes, $condition = '', $params = array()) {
        if (!is_array($attributes)) {
            throw new CException(Yii::t('CEAV', 'Attributes are required to build find criteria.'));
        }

        $criteria = new CDbCriteria;
        $criteria->condition = $condition;
        $criteria->params = $params;
        $criteria->join = 'LEFT JOIN ' . $this->tableName . ' ON ' . $this->getModelTableFkField() . ' = ' . $this->tableName . '.' . $this->entityField;
        $criteria->group = $this->getModelTableFkField();

        foreach ($attributes as $attribute => $values) {
            $attributeCondition = $this->tableName . '.' . $this->attributeField . ' = "' . $this->attributesPrefix . $attribute . '"';
            $valuesCondition = array();
            if (!is_array($values)) {
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
     * Returns criteria for deleting all attributes
     *
     * @return CdbCriteria
     */
    protected function getDeleteEavAttributesCriteria($attributes = array()) {
        $criteria = new CDbCriteria;
        $criteria->condition = $this->entityField . ' = :entity';
        $criteria->params = array(':entity' => $this->getModelTableFk());
        if (is_array($attributes)) {
            $criteria->addInCondition($this->attributeField, $attributes);
        }

        return $criteria;
    }

    /**
     * Loads attributes after finding model if preload is set
     *
     * @param CModelEvent
     */
    public function afterFind($event) {
        $attributes = array();
        if (is_array($this->safeAttributes) && !empty($this->safeAttributes)) {
            $attributes = $this->safeAttributes;
        }
        $this->loadEavAttributes($attributes);
        parent::afterFind($event);
    }

    /**
     * Save attributes after saving model
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
     * Delete attributes after deleting model
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