<?php
/**
 * EIconizedMenu
 *
 * Automatically adds favicons in front of menu links.
 *
 * @author Makarov Alexander
 * @version 1.1
 */
class EIconizedMenu extends CMenu {
    public $useSprites = true;
    public $yandexBaseUrl = 'http://favicon.yandex.net/favicon/';
    public $iconizerBaseUrl = 'http://www.google.com/s2/favicons?domain=';

	function init(){
		parent::init();
		if(!empty($this->htmlOptions['class']))
			$this->htmlOptions['class'].=' iconized';
		else
			$this->htmlOptions['class']='iconized';

		Yii::app()->clientScript->registerCssFile(
			Yii::app()->assetManager->publish(
				dirname(__FILE__).'/assets/iconizedMenu.css'
			)
		);

        if($this->useSprites){
            $domains = array();
            foreach($this->items as $item){
                $components = parse_url($item['url']);
                $domains[] = $components['host'];
		    }
            $spriteUrl = $this->yandexBaseUrl.implode('/', $domains);

            $offset = 0;
            foreach($this->items as &$item){
                $item['linkOptions']['style'] = "background-image: url($spriteUrl); background-position: 0 {$offset}px";
                $offset -= 16;
		    }
        }
        else {
            foreach($this->items as &$item){
                $components = parse_url($item['url']);
                $iconUrl = $this->iconizerBaseUrl.$components['host'];
                $item['linkOptions']['style'] = "background-image: url($iconUrl)";
		    }
        }
	}
}
