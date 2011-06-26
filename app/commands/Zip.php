<?php
/**
 * Zip
 */
class Zip extends ZipArchive {
	/**
	 * Adds directory to zip along with its subdirectories
	 *
	 * @param string $path path to directory to add to zip
	 * @param string $internalPath
	 * @return bool if adding to zip was successful
	 */
    public function addDir($path, $internalPath = ''){
		$this->addEmptyDir($internalPath);
        $nodes = glob($path.'/*');

        foreach ($nodes as $node) {
            $addAs = $internalPath.'/'.basename($node);
            if(is_dir($node)){
                $this->addDir($node, $addAs);
            }
            if(is_file($node)){
                if(!$this->addFile($node, $addAs))
					return false;
            }
        }

        return true;
    }
}
