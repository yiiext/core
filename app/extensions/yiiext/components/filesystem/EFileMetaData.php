<?php
/**
 * EFileMetaData class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1a
 */
class EFileMetaData extends CComponent {
    protected $filePath = '';
    protected $metaData = array();
    protected $attributes = array();

    public function __construct($filePath, $attributes = NULL) {
        $this->setFilePath($filePath);
        if ($attributes !== NULL) {
            $this->setAttributes($attributes); 
        }
    }
    protected function setFilePath($filePath) {
        $filePath = mb_convert_encoding($filePath, 'CP1251', 'UTF-8');
        $this->filePath = EFileHelper::realPath($filePath);
        if ($this->filePath === FALSE) {
            throw new CException(Yii::t('yiiext', 'File "{file}" not exists.',
                array('{file}' => $filePath)));
        }
        clearstatcache(TRUE, $this->filePath);
        return $this;
    }
    protected function getFilePath() {
        return mb_convert_encoding($this->filePath, 'UTF-8', 'CP1251');
    }
    public function setAttributes($attributes) {
        if (is_string($attributes)) {
            $attributes = preg_split('/,\s*/', $attributes);
            //$attributes = explode(',', $attributes);
        }
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
        return $this;
    }
    public function addAttribute($attribute) {
        if (is_string($attribute) && $attribute != '') {
            if ($this->getMetaData(($attribute)) && in_array($attribute, $this->attributes)) {
                $this->attributes[] = $attribute;
            }
        }
        return $this;
    }
    public function getAttributes() {
        return $this->attributes;
    }
    public function toArray() {
        return $this->metaData;
    }
    public function getMetaData($attribute) {
        if (!isset($this->metaData[$attribute])) {
            $this->metaData[$attribute] = $this->__get($attribute);
        }
        return $this->metaData[$attribute];
    }
    public function full() {
        return $this->setAttributes(array(
            'name', 'path', 'dirName',
            'type', 'extension', 'mime',
            'size', 'humanizeSize',
            'permissions', 'symbolicPermissions', 'octalPermissions',
            'modifiedTime', 'humanizeModifiedTime',
            'accessedTime', 'humanizeAccessedTime',
        ));
    }
    public function basic() {
        return $this->setAttributes(array(
            'name', 'path', 'extension', 'size',
             'permissions', 'modifiedTime', 'accessedTime',
        ));
    }
    public function getIsDir() {
        return is_dir($this->filePath);
    }
    public function getName() {
        return basename($this->getFilePath());
    }
    public function getPath() {
        return $this->getFilePath();
    }
    public function getDirName() {
        return dirname($this->getFilePath());
    }
    public function getType() {
        return filetype($this->filePath);
    }
    public function getExtension() {
        return EFileHelper::fileExtension($this->filePath);
    }
    public function getMime() {
        return EFileHelper::getMimeType($this->filePath);
    }
    public function getSize() {
        return filesize($this->filePath);
    }
    public function getHumanizeSize() {
        return EFileHelper::humanizeSize($this->getMetaData('size'));
    }
    public function getPermissions() {
        return fileperms($this->filePath);
    }
    public function getSymbolicPermissions() {
        return EFileHelper::permissionsToSymbolic($this->getMetaData('permissions'));
    }
    public function getOctalPermissions() {
        return EFileHelper::permissionsToOctal($this->getMetaData('permissions'));
    }
    public function getModifiedTime() {
        return filemtime($this->filePath);
    }
    public function getHumanizeModifiedTime() {
        return EFileHelper::humanizeTime($this->getMetaData('modifiedTime'));
    }
    public function getAccessedTime() {
        return fileatime($this->filePath);
    }
    public function getHumanizeAccessedTime() {
        return EFileHelper::humanizeTime($this->getMetaData('accessedTime'));
    }
}
// TODO scopes: short_info, full_info, ...
// TODO cache