<?php
/**
 * CStatusBehavior class file.
 *
 * Status behavior for models.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yii-slavco-dev/wiki/CStatusBehavior
 *
 * @version 0.4
 */
class CStatusBehavior extends CActiveRecordBehavior {
    /**
     * @var string The name of the table field where data is stored.
     * Required to set on init behavior. No default.
     */
    public $statusField = NULL;

    /**
     * @var array The possible statuses.
     * Default draft, published, archived
     */
    public $statuses = array('draft', 'published', 'archived');

    /**
     * @var string The status group.
     * Default default
     */
    public $statusGroup = 'default';

    protected $statusText = 'unknown';
    protected $statusTextTranslated = 'unknown';
    protected $status = NULL;

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
        // Check required var statusField.
        if (!is_string($this->statusField) || empty($this->statusField)) {
            throw new CException(self::t('yii', 'Property "{class}.{property}" is not defined.',
                array('{class}' => get_class($this), '{property}' => 'statusField')));
        }
        parent::attach($owner);
    }

    public function __toString() {
        return $this->getStatus();
    }

    /**
     * Set valid statuses values.
     *
     * @param array
     */
    public function setStatuses($statuses) {
        $this->statuses = is_array($statuses) && !empty($statuses)
            ? $statuses
            : array('draft', 'published', 'archived');
    }

    /**
     * Get valid status values.
     *
     * @param boolean prevent translate status.
     */
    public function getStatuses($translate = TRUE) {
        return $translate === FALSE
            ? $this->statuses
            : array_map(array($this, 'translateStatus'), $this->statuses);
    }

    /**
     * Return status group.
     *
     * @return string
     */
    public function getStatusGroup() {
        return is_string($this->statusGroup) && !empty($this->statusGroup)
            ? $this->statusGroup
            : 'default';
    }

    /**
     * Get model original status value from DB.
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Get model status text.
     *
     * @param boolean prevent translate status.
     * @return string
     */
    public function getStatusText($translate = TRUE) {
        return $translate === FALSE
            ? $this->statusText
            : $this->statusTextTranslated;
    }

    /**
     * Set status for model.
     *
     * @return CActiveRecord
     */
    public function setStatus($status) {
        if (($this->status = array_search($status, $this->statuses)) === FALSE)
            throw new CException(Yii::t(__CLASS__, 'Status "{status}" is not allowed.',
                array('{status}' => $status), __CLASS__));

        $this->getOwner()->{$this->statusField} = $this->status;
        $this->parseStatus();
        return $this->getOwner();
    }

    /**
     * Save status. Will be save only status attribute for model.
     *
     * @return boolean
     */
    public function saveStatus() {
        return $this->getOwner()->save(TRUE, array($this->statusField));
    }

    /**
     * Transform status value to text.
     *
     * @return CStatusBehavior
     */
    protected function parseStatus() {
        $this->statusText = isset($this->statuses[$this->getStatus()])
            ? $this->statuses[$this->getStatus()]
            : 'unknown';
        $this->statusTextTranslated = $this->translateStatus($this->statusText);
        return $this;
    }

    protected function translateStatus($statusName) {
        return Yii::t($this->getStatusGroup(), $statusName, array(), __CLASS__);
    }

    /**
     * Parse status after find model.
     *
     * @param CEvent 
     */
    public function afterFind($event) {
        $this->status = $this->getOwner()->{$this->statusField};
        $this->parseStatus();
        parent::afterFind($event);
    }    
}
