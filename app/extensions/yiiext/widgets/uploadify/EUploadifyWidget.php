<?php
/**
 * EUploadifyWidget class file.
 *
 * Uploadify jQuery plugin widget.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext
 * @uses http://www.uploadify.com/
 *
 * @version 1.2
 */
class EUploadifyWidget extends CWidget  {
    /**
     * Default options. For more info read {@link http://www.uploadify.com/documentation/ documents}
     *
     * @var array
     */
    private $defaults = array(
        // Options from source file
        'id' => 'uploadifyFileId',
        'expressInstall' => NULL,
        'displayData' => 'percentage',
        // Options from documents
        'uploader' => 'uploadify.swf',
        'script' => 'uploadify.php',
        'checkScript' => '',//check.php
        'scriptData' => array(),
        'fileDataName' => 'Filedata',
        'method' => 'POST',
        'scriptAccess' => 'sameDomain',
        'folder' => '',
        'queueID' => FALSE,
        'queueSizeLimit' => 999,
        'multi' => FALSE,
        'auto' => FALSE,
        'fileDesc' => '',
        'fileExt' => '',
        'sizeLimit' => FALSE,
        'simUploadLimit' => 1,
        'buttonText' => 'BROWSE',
        'buttonImg' => FALSE,
        'hideButton' => FALSE,
        'rollover' => FALSE,
        'height' => 30,
        'width' => 110,
        'wmode' => 'opaque',
        'cancelImg' => 'cancel.png',
        'onInit' => 'function(){}',
        'onSelect' => 'function(){}',
        'onSelectOnce' => 'function(){}',
        'onCancel' => 'function(){}',
        'onClearQueue' => 'function(){}',
        'onQueueFull' => 'function(){}',
        'onError' => 'function(event,queueID,fileObj,errorObj){}',
        'onOpen' => 'function(){}',
        'onProgress' => 'function(){}',
        'onComplete' => 'function(event,queueID,fileObj,response,data){}',
        'onAllComplete' => 'function(){}',
        'onCheck' => 'function(){}',
    );

    /**
     * Model with file-attribute.
     *
     * @var CFormModel
     */
    public $model = NULL;

    /**
     * Model attribute.
     *
     * @var string
     */
    public $modelAttribute = 'uploadifyFile';

    /**
     * Extension settings.
     *
     * @var array
     */
    private $settings = array();

    private $scriptPath = '';

    public function __construct() {
        $this->scriptPath = dirname(__FILE__).'/vendors';
        // Publish defaults assets
        $am = Yii::app()->getAssetManager();
        $this->settings['uploader'] = $am->publish($this->scriptPath.'/uploadify.swf');
        $this->settings['cancelImg'] = $am->publish($this->scriptPath.'/cancel.png');
        $this->settings['expressInstall'] = $am->publish($this->scriptPath.'/expressInstall.swf');
    }

    public function __get($var) {
        if (array_key_exists($var, $this->defaults)) {
            return isset($this->settings[$var]) ? $this->settings[$var] : $this->defaults[$var];
        }
        return parent::__get($var);
    }

    /**
     * Setter for settings. Check if exists and different in defaults.
     *
     * @param array
     */
    public function setSettings($settings) {
        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $this->defaults) && $this->defaults[$key] != $value) {
                $this->settings[$key] = $value;
            }
        }
    }

    /**
     * Encode settings array into json format.
     *
     * @return string
     */
    private function getJsonSettings() {
        $settings = array();
        foreach ($this->settings as $key => $value) {
            if (substr($key, 0, 2) == 'on') $settings[] = $key . ':' . $value;
            else $settings[] = json_encode($key) . ':' . json_encode($value);
        }
        return "{\n" . implode(",\n\t", $settings) . '}';
    }
    
    public function init() {
        // Register scripts and styles files.
        $am = Yii::app()->getAssetManager();
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($am->publish($this->scriptPath.'/jquery.uploadify.v2.1.0.min.js'));
        $cs->registerScriptFile($am->publish($this->scriptPath.'/swfobject.js'));
        $cs->registerCssFile($am->publish($this->scriptPath.'/uploadify.css'));

        // fileDesc is required if fileExt set.
        if (!empty($this->settings['fileExt']) && empty($this->settings['fileDesc'])) {
            $this->settings['fileDesc'] = 'Supported files (' . $this->settings['fileExt'] . ')';
        }

        // Generate fileDataName for linked with model attribute.
        $this->settings['fileDataName'] = get_class($this->model) . '[' . $this->modelAttribute . ']';
    }

    public function run() {
        echo CHtml::activeFileField($this->model, $this->modelAttribute, array('id' => $this->id))."\n";

        $cs = Yii::app()->clientScript;
        $cs->registerScript('loadUploadify',
            'jQuery("#' . $this->id . '").uploadify(' . $this->getJsonSettings() . ');',
            CClientScript::POS_READY);
    }
}
