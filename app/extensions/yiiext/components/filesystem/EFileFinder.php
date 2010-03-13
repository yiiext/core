<?php
/**
 * EFileFinder class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.filesystem
 */
class EFileFinder {
    public $count = 0;
    public $files = array();

    public function __construct($dir, $criteria = NULL) {
        $this->find($dir, $criteria);
    }

    public function __toString() {
        return $this->files;
    }

    protected function find($dir, $criteria = NULL) {
        if (!($criteria instanceof EFileCriteria)) {
            $criteria = new EFileCriteria($criteria);
        }
        $handle = opendir($dir); 
        if ($criteria->limit == 0 || $this->count < $criteria->limit) {
            while (($file = readdir($handle)) !== FALSE) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (!@fnmatch($criteria->pattern, $file)) {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                //TODO: временная проверка. в винде есть файлы которые не названны не верными символоми 
                if (!file_exists(realpath($path))) {
                    continue;
                }
                $file = EFile::getInstance($path);
                //TODO: запуск валидаторов
                if ($file->isDir && $criteria->depth) {
                    $this->find($path, $criteria->mergeWith(array('depth' => $criteria->depth - 1)));
                }
                else {
                    $this->files[] = $file;
                    $this->count++;
                }
            }
        }
        closedir($handle);
        return $this;
    }
}