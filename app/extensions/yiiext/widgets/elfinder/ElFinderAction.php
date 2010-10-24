<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(__FILE__).'/elFinder.class.php');
/**
 * Description of ElrteAction
 *
 * @author root
 */
class ElFinderAction extends CAction
{
    public $options=array();
    
    public function  run()
    {
        $this->options=array_merge($this->options,array(
            'root'=>Yii::getPathOfAlias('webroot.upload'),
            'URL'=>Yii::app()->baseUrl.'/upload/',
            'rootAlias'=>'Home',
        ));

        $fm = new elFinder($this->options);
        $fm->run();
    }

    public function  __set($name, $value)
    {
        if(!$this->canSetProperty($name))
            $this->options[$name]=$value;
        else parent::__set($name, $value);
    }

    public function __get($name)
    {
        if(!$this->canGetProperty($name))
            return $this->options[$name];
        else return parent::__get($name);
    }
}
?>
