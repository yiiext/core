<?php
/**
 * ReleaseCommand
 */
class ReleaseCommand extends CConsoleCommand {
    function run($args){
        if(empty($args[1])){
            echo $this->getHelp();
            return;
        }

        $path = $args[0];
        $version = $args[1];

        echo "Preparing $path release.\n";

        require dirname(__FILE__).'/GenerateDocsCommand.php';

        $docProcessor = new GenerateDocsCommand($this->getName(), $this->getCommandRunner());
        $outFiles = $docProcessor->processDocuments($path);

        // copy extension dir to temp
        $extPath = Yii::getPathOfAlias('ext').'/'.$path;
        $copiedExtRoot = Yii::getPathOfAlias('application.runtime.extension');

        echo "Removing $copiedExtRoot.\n";
        if(file_exists($copiedExtRoot)) $this->recursiveDelete($copiedExtRoot);

        $copiedExtPath = $copiedExtRoot.'/'.$path;
        if(!file_exists($copiedExtPath)) mkdir($copiedExtPath, 0777, true);

        echo "Copying extension files from $extPath to $copiedExtPath.\n";

        CFileHelper::copyDirectory($extPath, $copiedExtPath, array(
            'exclude' => array(
                '.svn',
                'readme_en.txt',
                'readme_ru.txt',
            ),
        ));

        echo "Copying documentation to $copiedExtPath.\n";

        foreach($outFiles as $file){
            copy($file, $copiedExtPath.'/'.basename($file));
        }

		$pathExp = explode('/', $path);
        $zipName = end($pathExp).'_'.$version.'.zip';
        $releasePath = Yii::getPathOfAlias('application.releases');
        if(!file_exists($releasePath)) mkdir($releasePath, 0777, true);

        $zipPath = "$releasePath/$zipName";
        if(file_exists($zipPath)) unlink($zipPath);
        //touch($zipPath);

        echo "Creating Zip $zipPath.\n";

        require dirname(__FILE__).'/Zip.php';
        $zip = new Zip();
        if($zip->open($zipPath, ZipArchive::OVERWRITE | ZipArchive::CREATE)!==TRUE) {
            die("Failed to open Zip $zipPath.\n");
        }

        if(!$zip->addDir($copiedExtRoot)){
            die("Failed adding $copiedExtRoot to Zip.\n");
        }

        if($zip->close()){
            echo "Done.\n";
        }
        else {
            die("Failed to write Zip $zipPath.\n");
        }
    }

    function getHelp(){
        return 'Usage: '.$this->getCommandRunner()->getScriptName().' '.$this->getName()
        .' yiiext/path/to/extension/dir 1.0';
    }

    /**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    function recursiveDelete($str){
        if(is_file($str)){
            return unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return rmdir($str);
        }
    }
}
