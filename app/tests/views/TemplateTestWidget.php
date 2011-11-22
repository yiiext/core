<?php

/**
 * This widget renders a template to test template scopes of smarty renderer
 */
class TemplateTestWidget extends CWidget
{
	public $template = null;
	public $level = 3;

	public function getId()
	{
		return 'widgetId' . $this->level;
	}

	public function run()
	{
		$this->render($this->template, array(
			'template'=>$this->template,
			'var1'=>'widget' . $this->level,
			'level'=>$this->level-1
		));
	}
}
