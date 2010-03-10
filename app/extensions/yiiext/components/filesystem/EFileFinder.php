<?php
/**
 * EFileFinder class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class EFileFinder {

    public function find($dir) {
        return self::findFilesRecursive($dir); // лимит в один файл
    }

    public function findAll($dir) {
        return self::findFilesRecursive($dir);
    }

    protected static function findFilesRecursive($dir) {
        $list = array();
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== FALSE) {
            if ($file==='.' || $file==='..') {
                continue;
            }
            $file = EFile::getInstance($dir . DIRECTORY_SEPARATOR . $file);
            //TODO: запуск валидаторов 
            $list[] = $file;
        }
        closedir($handle);
        return $list;
    }
}
