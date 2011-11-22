<?php

Yii::import('ext.yiiext.renderers.smarty.ESmartyViewRenderer');

class ESmartyViewRendererTest extends CTestCase
{
	/**
	 * @var ESmartyViewRenderer
	 */
	public $smarty;

	public function setUp()
	{
		$this->smarty = new ESmartyViewRenderer();
		$this->smarty->init();
	}

	public function matrixProvider()
	{
		$simpleTemplate = Yii::getPathOfAlias('application.tests.views') . '/simpleTemplate.tpl';
		$nestedTemplate = Yii::getPathOfAlias('application.tests.views') . '/nestedTemplate.tpl';
		return array(
			//    return, template,               values
			array(true,   $simpleTemplate,  array('var1'=>'value1'), '<b>value1</b>test'."\n"),
			array(false,  $simpleTemplate,  array('var1'=>'value1'), '<b>value1</b>test'."\n"),

			array(true,   $nestedTemplate,  array('var1'=>'value1', 'template'=>'application.tests.views.nestedTemplate'), "<b>value1</b><b>2</b>2\nvalue1\n"),
			array(false,  $nestedTemplate,  array('var1'=>'value1', 'template'=>'application.tests.views.nestedTemplate'), "<b>value1</b><b>2</b>2\nvalue1\n"),
		);
	}

	/**
	 * @dataProvider matrixProvider
	 */
	public function testSimpleTemplateRender($return, $template, $data, $content)
	{
		Yii::app()->setComponent('viewRenderer', $this->smarty);

		ob_start();
		$controller = new CController('test');
		$return = $this->smarty->renderFile($controller, $template, $data, $return);
		$output = ob_get_clean();

		if ($return) {
			$this->assertEquals('', $output, 'renderFile produced output, when it should return');
			$this->assertEquals($content, $return, 'renderFile did not return correctly rendered template');
		} else {
			$this->assertEquals($content, $output, 'renderFile did not output correctly rendered template');
			$this->assertEquals(null, $return, 'renderFile did return something when it should ouput');
		}
	}


}
