<?php
/**
 * Zip
 */
class Zip extends ZipArchive {
    public function addDir($path, $internalPath) {
        $this->addEmptyDir($internalPath);
        $nodes = glob($path.'/*');

        foreach ($nodes as $node) {
            $addAs = $internalPath.'/'.basename($node);
            if (is_dir($node)) {
                $this->addDir($node, $internalPath.'/'.basename($node));
            }
            if (is_file($node)) {
                if(!$this->addFile($node, $addAs)) return false;
            }
        }

        return true;
    }
}
