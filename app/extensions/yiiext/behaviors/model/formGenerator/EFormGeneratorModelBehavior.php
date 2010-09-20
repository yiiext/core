<?php
/**
 * EFormGeneratorModelBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormGeneratorModelBehavior represents a form generator for specific model.
 *
 * This behavior is extends {@link EFormGeneratorBehavior} with one difference,
 * for form will be used model to which is attached behavior.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.behaviors.model.formGenerator
 */
require_once(dirname(__FILE__).'/EFormGeneratorBehavior.php');
class EFormGeneratorModelBehavior extends EFormGeneratorBehavior
{
	/**
	 * Create main form.
	 * @param array $config form configuration.
	 * @param CModel $model form model.
	 * @param CBaseController $owner instance for render CActiveForm widget
	 * @return EFormGeneratorBehavior
	 */
	public function createForm($config=array(),$model=null,$owner=null)
	{
		if($model===null)
			$model=$this->getOwner();

		return parent::createForm($config,$model,$owner);
	}
}
