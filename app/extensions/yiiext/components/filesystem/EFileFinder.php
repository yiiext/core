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
        //TODO: вызов поиска в конструкторе, не совсем хорошее занятие
        $this->find($dir, $criteria);
    }

    public function __toString() {
        return $this->files;
    }

    protected function addFile(EFile $file, $sort = FALSE) {
        //TODO: устроить сортировку сразу в цикле проверки файлов
        $this->files[] = $file;
        $this->count++;
    }

    //TODO: не использовать критерий при рекурсии?
    protected function find($dir, $criteria = NULL) {
        if (!($criteria instanceof EFileCriteria)) {
            $criteria = new EFileCriteria($criteria);
        }
        $handle = opendir($dir);
        while (($fileName = readdir($handle)) !== FALSE) {
            if ($criteria->limit != 0 && $this->count >= $criteria->limit) {
                break;
            }
            if ($fileName === '.' || $fileName === '..') {
                continue;
            }
            $file = EFile::getInstance($dir . DIRECTORY_SEPARATOR . $fileName);
            if ($file->validate($criteria)) {
                if ($file->isDir && ($depth = $criteria->depth)) {
                    $this->find($file->path, $criteria->mergeWith(array('depth' => $depth - 1)));
                }
                else {
                    $this->addFile($file);
                }
            }
        }
        closedir($handle);
        return $this;
    }
}