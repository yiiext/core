<?php
/**
 * Console command to generate HTML docs for extensions from
 * readme_ru.txt and readme_en.txt (markdown).
 */
class GenerateDocsCommand extends CConsoleCommand {
    function run($args){
        $paths = array();

        // extension name specified
		if(!empty($args)){
            foreach($args as $arg){
                $paths[] = Yii::getPathOfAlias('ext').'/'.$arg;
            }			 
		}
        // check all extensions
        else {
            foreach (new DirectoryIterator(Yii::getPathOfAlias('ext')) as $fileInfo) {
                if(!$fileInfo->isDot() && $fileInfo->isDir() && $fileInfo->getFilename()!='.svn'){
                    $paths[] = Yii::getPathOfAlias('ext').'/'.$fileInfo->getFilename();
                }
            }
        }

        // process documents
        foreach($paths as $path){
            if(file_exists($path.'/readme_en.txt')){
                $this->processDocument($path.'/readme_en.txt');
            }

            if(file_exists($path.'/readme_ru.txt')){
                $this->processDocument($path.'/readme_ru.txt');
            }
        }

        echo "Done.\n";
	}

    private function processDocument($path){
        echo "Processing $path.\n";
        $markdownParser = new CMarkdownParser();
        $layout = Yii::getPathOfAlias('application.views.layouts').'/documentation.php';
        $f = fopen($path, "r");
        $title = fgets($f);
        fclose($f);
        $in = file_get_contents($path);
        $out = $markdownParser->transform($in);
        $out = $this->renderFile($layout, array('content' => $out, 'title' => $title), true);
        file_put_contents(str_replace('.txt', '.html', $path), $out);
    }
}