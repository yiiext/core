<?php
/**
 * EMenuWidget class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @version 0.1
 * @package yiiext.widgets.menu
 * @see CMenu
 */
Yii::import('zii.widgets.CMenu');
class EMenuWidget extends CMenu
{
	/**
	 * @var boolean whether active menu item will be rendered as a span text.
	 * Defaults to false.
	 */
	public $activeLinkDisable=false;
	/**
	 * @see CMenu::renderMenuRecursive
	 */
	protected function renderMenuRecursive($items)
	{
		foreach($items as $item)
		{
			echo CHtml::openTag('li', isset($item['htmlOptions']) ? $item['htmlOptions'] : array());

			if(isset($item['encodeLabel']) ? $item['encodeLabel'] : $this->encodeLabel)
				$item['label']=CHtml::encode($item['label']);

			if(isset($item['url']) && !($this->activeLinkDisable && $item['active']))
				$menu=CHtml::link($item['label'],$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
			else
				$menu=CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);

			if(isset($this->itemTemplate) || isset($item['template']))
			{
				$template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template,array('{menu}'=>$menu));
			}
			else
				echo $menu;

			if(isset($item['items']) && count($item['items']))
			{
				echo "\n".CHtml::openTag('ul',CMap::mergeArray($this->submenuHtmlOptions,$item['submenuHtmlOptions'] ? $item['submenuHtmlOptions'] : array()))."\n";
				$this->renderMenuRecursive($item['items']);
				echo CHtml::closeTag('ul')."\n";
			}
			echo CHtml::closeTag('li')."\n";
		}
	}
	/**
	 * @see CMenu::normalizeItems
	 */
	protected function normalizeItems($items,$route,&$active)
	{
		foreach($items as $i=>$item)
		{
			if(isset($item['visible']) && !$item['visible'])
			{
				unset($items[$i]);
				continue;
			}
			
			$hasActiveChild=false;
			if(isset($item['items']))
			{
				$items[$i]['items']=$this->normalizeItems($item['items'],$route,$hasActiveChild);
				if(empty($items[$i]['items']) && $this->hideEmptyItems)
					unset($items[$i]['items']);
			}
			
			if(!isset($item['active']))
			{
				if($this->activateParents && $hasActiveChild || $this->isItemActive($item,$route))
					$active=$items[$i]['active']=true;
				else
					$items[$i]['active']=false;
			}
			else if($item['active'])
				$active=true;
			
			if($items[$i]['active'] && $this->activeCssClass!='')
			{
				if(isset($item['htmlOptions']['class']))
					$items[$i]['htmlOptions']['class'].=' '.$this->activeCssClass;
				else
					$items[$i]['htmlOptions']['class']=$this->activeCssClass;
			}
		}
		return array_values($items);
	}
}
