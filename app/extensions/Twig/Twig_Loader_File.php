<?php
/**
 * Loads template from a file given full path without any restiction
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 *
 * @version 0.9
 */
class Twig_Loader_File extends Twig_Loader {
    /**
     * Gets the source code of a template, given its full path.
     *
     * @param  string $path full path to template
     *
     * @return array An array consisting of the source code as the first element,
     *               and the last modification time as the second one
     *               or false if it's not relevant
     */
    public function getSource($path) {
        $file = realpath($path);

        if(!file_exists($file)) {
            throw new RuntimeException(sprintf('Unable to find template "%s".', $path));
        }

        return array(file_get_contents($file), filemtime($file));
    }

    /**
     * Loads a template by full path.
     *
     * @param  string $path full path to template
     *
     * @return string The class name of the compiled template
     */
    public function load($path){
        $name = pathinfo($path, PATHINFO_FILENAME);
        $cls = $this->getTemplateName($name);

        // need 'false' not to trigger autoload
        if (class_exists($cls, false)) {
            return $cls;
        }

        list($template, $mtime) = $this->getSource($path);

        if (false === $this->cache) {
            $this->evalString($template, $name);

            return $cls;
        }

        $cache = $this->getCacheFilename($name);
        if (!file_exists($cache) || false === $mtime || ($this->autoReload && (filemtime($cache) < $mtime))) {
            $fp = @fopen($cache, 'wb');
            if (!$fp) {
                $this->evalString($template, $name);

                return $cls;
            }
            file_put_contents($cache, $this->compile($template, $name));
            fclose($fp);
        }

        require_once $cache;

        return $cls;
    }
}
