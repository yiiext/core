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
 * @version 0.4
 *
 * @todo Lazy loading
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
    public $modelTableFk = NULL;

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
        // Prepare translate component for behavior messages.
        if (!Yii::app()->hasComponent(__CLASS__)) {
            Yii::app()->setComponents(array(
                __CLASS__ => array(
                    'class' => 'CPhpMessageSource',
                    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages',
                )
            ));
        }
        // tableName is required
        if (!is_string($this->tableName) || empty($this->tableName)) {
            throw new CException(self::t('yii', 'Property "{class}.{property}" is not defined.',
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
        throw new CException(Yii::t(__CLASS__, 'Cannot get model table foreign key.', array(), __CLASS__));
    }
    private function getModelTableFk() {
        $modelTableFk = $this->getModelTableFkField();
        return $this->getOwner()->$modelTableFk;
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
        return NULL;
    }

    /**
     * Set attribute value.
     *
     * @param string attribute name
     * @param mixed attribute value
     * @return CActiveRecord
     */
    public function setEavAttribute($attribute, $value) {
        if ($this->checkEavAttribute($attribute)) {
            $this->attributes[$attribute] = $value;
            // remember changed attribute
            $this->attributesForSave[$this->attributesPrefix . $attribute] = $value;
        }
        return $this->getOwner();
    }

    /**
     * Set attributes values.
     *
     * @param array attribute => value
     * @return CActiveRecord
     */
    public function setEavAttributes($attributes) {
        if (is_array($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $this->setEavAttribute($attribute, $value);
            }
        }
        return $this->getOwner();
    }

    /**
     * Delete all or specified attributes.
     *
     * @param array $attributes
     * @return CActiveRecord
     */
    public function deleteEavAttributes($attributes = array()) {
        is_array($attributes) || $attributes = array($attributes);
        !empty($attributes) || $attributes = array_keys($this->attributes);
        foreach ($attributes as $attribute) {
            $this->setEavAttribute($attribute, NULL);
        }
        return $this->getOwner();
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
     * @return boolean
     */
    protected function createSaveEavAttributeCommand($attribute, $value) {
        $data = array(
            $this->entityField => $this->getModelTableFk(),
            $this->attributeField => $attribute,
            $this->valueField => $value,
        );
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
     * Returns criteria for deleting all attributes
     *
     * @return CdbCriteria
     */
    protected function getDeleteEavAttributesCriteria($attributes = array()) {
        $criteria = new CDbCriteria;
        $criteria->condition = $this->entityField . ' = :entity';
        $criteria->params = array(':entity' => $this->getModelTableFk());
        if (is_array($attributes) && !empty($attributes)) {
            $criteria->addInCondition($this->attributeField, $attributes);
        }
        return $criteria;
    }

    /**
     * Get criteria to limit query by eav-attributes
     *
     * @access protected
     * @param $attributes
     * @return CDbCriteria
     */
    protected function getFindByEavAttributesCriteria($attributes){
        $criteria = new CDbCriteria();
        $pk = $this->getModelTableFkField();

        if (!empty($attributes)){
            $conn = $this->getOwner()->dbConnection;
            $i = 0;
            foreach ($attributes as $attribute => $values) {
                // If search models with attribute name with specified values.
                if (is_string($attribute)) {
                    $attribute = $conn->quoteValue($attribute);
                    if (!is_array($values)) $values = array($values);
                    foreach ($values as $value) {
                        $value = $conn->quoteValue($value);
                        $criteria->join .= "\nJOIN {$this->tableName} eavb$i"
                                        .  "\nON {$this->getOwner()->tableName()}.{$pk} = eavb$i.{$this->entityField}"
                                        .  "\nAND eavb$i.{$this->attributeField} = $attribute"
                                        .  "\nAND eavb$i.{$this->valueField} = $value";
                        $i++;
                    }
                }
                // If search models with attribute name with anything values.
                elseif (is_int($attribute)) {
                    $values = $conn->quoteValue($values);
                    $criteria->join .= "\nJOIN {$this->tableName} eavb$i"
                                    .  "\nON {$this->getOwner()->tableName()}.{$pk} = eavb$i.{$this->entityField}"
                                    .  "\nAND eavb$i.{$this->attributeField} = $values";
                    $i++;
                }
            }
            $criteria->distinct = TRUE;
            $criteria->group .= "{$this->getOwner()->tableName()}.{$pk}";
        }
        return $criteria;
    }

    /**
     * Limit current AR query to have all attributes and values specified
     *
     * @param string|array $attributes
     * @param string|array $values
     * @return CActiveRecord
     */
    public function withEavAttributes($attributes = array()) {
        // Create array for convenience.
        if (is_string($attributes)) {
            $attributes = array($attributes);
        }

        // If not set attributes, search models with anything attributes exists.
        if (is_array($attributes) && empty($attributes)) {
            $attributes = $this->safeAttributes;
        }

        // $attributes be array of elements: $attribute => $values
        if (is_array($attributes)) {
            $criteria = $this->getFindByEavAttributesCriteria($attributes);
            $this->getOwner()->getDbCriteria()->mergeWith($criteria);
        }
        return $this->getOwner();
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
                // if null, delete attribute from db
                if (is_null($values)) return FALSE;
                // create array of values for convenience
                if (!is_array($values)) $values = array($values);
                foreach ($values as $value) {
                    $this->createSaveEavAttributeCommand($attribute, $value)->execute();
                }
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
            ->createDeleteCommand($this->tableName, $this->getDeleteEavAttributesCriteria())
            ->execute();

        parent::afterDelete($event);
    }
}