example:

$file = EFile::getInstance(__FILE__);
CVarDumper::dump($file->getMetaData()->toArray(), TRUE, TRUE);