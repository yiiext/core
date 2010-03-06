<?php
/**
 * Console command to generate HTML docs for extensions from
 * readme_ru.txt and readme_en.txt (markdown).
 */
class GenerateDocsCommand extends CConsoleCommand {
    function run($args){
		if(empty($args)){
            echo $this->getHelp();
            return;            
        }

        $path = $args[0];			 		        

        // process documents
        $this->processDocuments($path);
    }

    public function processDocuments($path){
        echo "Generating documentation for $path.\n";

        $outFiles = array();
        
        $outFiles[] = $this->processDocument($path, 'readme_en.txt');
        $outFiles[] = $this->processDocument($path, 'readme_ru.txt');

        echo "Done.\n";

        return $outFiles;
    }    

    private function processDocument($path, $filename){
        $basePath = Yii::getPathOfAlias('ext').'/';
        $filePath = $basePath.$path.'/'.$filename;

        if(!file_exists($filePath)){
            echo "File $filePath does not exist. Skipping.\n";
            return;
        }

        echo "Processing $filePath.\n";
        
        $markdownParser = new CMarkdownParser();
        $layout = Yii::getPathOfAlias('application.views.layouts').'/documentation.php';
        
        if($f = fopen($filePath, "r")){        
            $title = $in = fgets($f);
            while(!feof($f)){
                $in .= fgets($f, 4096);                        
            }
        }
        fclose($f);
                 
        $out = $markdownParser->transform($in);
        $out = $this->renderFile($layout, array('content' => $out, 'title' => $title), true);

        $docsPath = Yii::getPathOfAlias('application.docs').'/';
        if(!file_exists($docsPath.$path)) mkdir($docsPath.$path, 777, true);

        $outFileName = $docsPath.$path.'/'.str_replace('.txt', '.html', $filename);
        file_put_contents($outFileName, $out);
        return $outFileName;
    }

    function getHelp(){
        return 'Usage: '.$this->getCommandRunner()->getScriptName().' '.$this->getName()
        .' yiiext/path/to/extension/dir';
    }
}