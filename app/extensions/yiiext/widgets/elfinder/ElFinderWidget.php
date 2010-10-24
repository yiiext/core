<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ElFinderWidget
 *
 * @author root
 */
class ElFinderWidget extends CWidget
{
    public $options=array();
    public $htmlOptions=array();

    public function  run()
    {
        if(!isset($this->options['url']))
            throw new CException('Вы не указали ссылку к коннектору');

        $this->htmlOptions['id']=$this->getId();
        echo CHtml::tag('div',$this->htmlOptions);

        $a=CHtml::asset(dirname(__FILE__).'/assets/');
        $cs=Yii::app()->clientScript;

        $cs->registerCssFile($a.'/css/elfinder.css');
        $cs->registerCssFile($a.'/js/ui-themes/base/ui.all.css');

        $cs->registerCoreScript('jquery.ui');
        $cs->registerScriptFile($a.'/js/elfinder.min.js');
        if(!empty($this->options['lang']))
            $cs->registerScriptFile($a.'/js/i18n/elfinder.'.$this->options['lang'].'.js');

        $cs->registerScript('el'.$this->getId(),'jQuery("#'.$this->getId().'").elfinder('.CJavaScript::encode($this->options).')',  CClientScript::POS_READY);

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

    //put your code here
}