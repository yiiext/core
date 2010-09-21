<?php
/**
 * EIconizedMenu
 *
 * Automatically adds favicons in front of menu links.
 *
 * @author Makarov Alexander
 * @version 1.0
 *
 * Alternative iconizer URLs:
 * http://favicon.yandex.ru/favicon/
 */
class EIconizedMenu extends CMenu {
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

		foreach($this->items as &$item){
			$components = parse_url($item['url']);
			$iconUrl = $this->iconizerBaseUrl.$components['host'];
			$item['linkOptions']['style'] = "background-image: url($iconUrl)";
		}
	}
}
