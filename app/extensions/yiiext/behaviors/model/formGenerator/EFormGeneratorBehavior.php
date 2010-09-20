<?php
/**
 * EFormGeneratorBehavior class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/mit-license.php
 */
/**
 * EFormGeneratorBehavior.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.behaviors.model.formGenerator
 */
class EFormGeneratorBehavior extends CBehavior
{
	protected $_form;
	public $config=array();

	public function createForm($config=array())
	{
		return $this->_form=new CForm($config,null,$this->getOwner());
	}
	public function getModelElements($model)
	{
		$elements=array();
		if($model instanceof CModel)
		{
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
		}
		return $elements;
	}
	public function setElements($elements)
	{
		$this->getForm()->setElements($elements);
	}
	public function setModel($model)
	{
		$elements=$this->getModelElements($model);
		$this->getForm()->setModel($model);
		$this->setElements($elements);
		return $this;
	}
	public function addModel($model)
	{
		if($this->getForm()->getModel()===null)
			$this->setModel($model);
		else if($elements=$this->getModelElements($model))
		{
			$elements=array(
				get_class($model)=>array(
					'type'=>'form',
					'model'=>$model,
					'elements'=>$elements,
				)
			);
			$this->setElements($elements);
		}
		return $this;
	}
	public function addFrom($form)
	{
		$this->setElements(array(
			$this->getForm()->getElements()->count()=>$form
		));
	}
	public function getForm()
	{
		if($this->_form===null)
		{
			$config=new CConfiguration(dirname(__FILE__).'/config.php');
			$config->mergeWith($this->config);
			$this->createForm($config->toArray());
		}

		return $this->_form;
	}
	public function render()
	{
		echo $this->_form->render();
	}
}
