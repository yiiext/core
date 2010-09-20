<?php
/**
 * EModelElementsGenerator class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EModelElementsGenerator.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.behaviors.model.formGenerator
 */
class EModelElementsGenerator extends CModelBehavior
{
	public function getFormElements()
	{
		$elements=array();
		$model=$this->getOwner();
		foreach($model->getAttributes() as $attribute=>$value)
			$elements[$attribute]=array(
				'type'=>'text',
			);
		return $elements;
	}
}
