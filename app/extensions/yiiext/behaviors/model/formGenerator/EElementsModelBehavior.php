<?php
/**
 * EElementsModelBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EElementsModelBehavior generate array of default text elements based of {@link CModel::getAttributes()}.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.behaviors.model.formGenerator
 */
class EElementsModelBehavior extends CModelBehavior
{
	/**
	 * Get array elements based of {@link CModel::getAttributes()}.
	 * Type of all elements will be text.
	 * @todo generate other types for inputs
	 * @return array
	 */
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
