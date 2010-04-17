<?php
/**
 * EFancyboxConfiguration class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.gallery
 */
class EFancyboxConfiguration extends CConfiguration
{
	private $_def = NULL;
	private $_c = NULL;
	private $_k = NULL;
	public function __construct($data = NULL, $defaults = NULL, $conditions = NULL)
	{
		$this->_def = new CConfiguration($defaults);
		$this->_k = $this->_def->getKeys();
		$this->_c = new CConfiguration($conditions);
		parent::__construct($data);
	}
	public function getDefaults()
	{
		return $this->_def->toArray();
	}
	public function getConditions()
	{
		return $this->_c->toArray();
	}
	public function add($key, $value)
	{
		if ($this->hasValidKey($key))
		{
			if ($this->_def->itemAt($key) == $value)
				return;
			if (is_array($c = $this->_c->itemAt($key)) && !in_array($value, $c))
				return;

			parent::add($key, $value);
		}
	}
	public function contains($key)
	{
		parent::contains($key);
	}
	public function hasValidKey($key)
	{
		return in_array($key, $this->_k);
	}
}
