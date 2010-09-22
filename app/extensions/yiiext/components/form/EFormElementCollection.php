<?php
/**
 * EFormElementCollection class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormElementCollection implements the collection for storing form elements.
 *
 * EFormElementCollection extended version of {@link CFormElementCollection}.
 * Added possibility to add fourth type of value - a {@link CModel}.
 * Internally, these values will be converted to {@link CFormElement}
 * that returned from method CModel::getFormElements().
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.components.form
 */
class EFormElementCollection extends CFormElementCollection
{
	protected $_form;
	protected $_forButtons;

	public function __construct($form,$forButtons=false)
	{
		parent::__construct($form,$forButtons);
		$this->_form=$form;
		$this->_forButtons=$forButtons;
	}

	public function add($key,$value)
	{
		if($value instanceof CModel && $elements=self::getModelElements($value))
		{
			$elements=array(
				get_class($value)=>array(
					'type'=>'form',
					'model'=>$value,
					'elements'=>$elements,
				)
			);
			$this->_form->setElements($elements);
		}
		else
			parent::add($key,$value);
	}
	/**
	 * Get elements from model.
	 * @static
	 * @param CModel $model
	 * @return array
	 */
	public static function getModelElements($model)
	{
		$elements=array();
		$scenario=$model->getScenario();
		foreach($model->getFormElements() as $name=>$element)
		{
			$on=array();
			if(isset($element['on']) && is_array($element['on']))
				$on=$element['on'];
			else if(isset($element['on']))
				$on=preg_split('/[\s,]+/',$element['on'],-1,PREG_SPLIT_NO_EMPTY);

			if(empty($on) || in_array($scenario,$on))
			{
				unset($element['on']);
				$elements[$name]=$element;
			}
		}
		return $elements;
	}
}
