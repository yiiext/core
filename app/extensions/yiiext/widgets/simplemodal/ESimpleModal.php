<?php
/**
 * ESimpleModal
 */
class ESimpleModal extends CWidget {
	public $triggerElementSelector = '';
	public $content = "";

	function init(){
		Yii::app()->clientScript->registerScriptFile(
    		Yii::app()->assetManager->publish(
				dirname(__FILE__).'/js/jquery.simplemodal-1.3.4.min.js'
    		)
		);
	}

	function run(){
		$id = $this->getId();
		echo CHtml::openTag('div', array('id' => $id, 'style' => 'display: none'));
		echo $this->content;
		echo CHtml::closeTag('div');		

		Yii::app()->clientScript->registerScript($id, "
		$('$this->triggerElementSelector').click(function(){
			$('#$id').modal();
		});
		");
	}
}
