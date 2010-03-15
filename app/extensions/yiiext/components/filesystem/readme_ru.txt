Доступ к метаданным файла

$file = EFile::getInstance(__FILE__);
CVarDumper::dump($file->getMetaData()->toArray(), 1, TRUE);

Поиск файлов в директории
$rules = array(
    //array('name', 'pattern' => '*tree*'),
    array('size', 'minSize' => 2048, 'maxSize' => 3000),
    //array('extension', 'extension' => array('css', 'js'), 'skipDir' => TRUE),
);
$dir = EFile::getInstance(dirname(__FILE__));
$files = $dir->find($rules);
CVarDumper::dump($files->count, 1, TRUE);
CVarDumper::dump($files, 10, TRUE);
