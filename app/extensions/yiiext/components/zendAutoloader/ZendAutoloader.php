<?php
/**
 * ZendAutoloader
 *
 * @author Alexander Makarov
 * @version 1.0
 * 
 * See readme for instructions. 
 */
class ZendAutoloader {
    static $basePath = null;

    /**
     * Class autoload loader.
     *
     * @static
     * @param string $class
     * @return boolean
     */
    static function loadClass($className){                
        if(strpos($className, 'Zend_')!==false){            
            if(!self::$basePath) self::$basePath = Yii::getPathOfAlias("application.vendors").'/';
            include self::$basePath.str_replace('_','/',$className).'.php';
            return class_exists($className, false) || interface_exists($className, false);
        }
        return false;
    }
}
