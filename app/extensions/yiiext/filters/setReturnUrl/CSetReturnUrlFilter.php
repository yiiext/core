<?php
/**
* SetReturnUrlFilter
*
* Позволяет сохранять текущий url в сессии для всех или выборочных действий
* контроллера, чтобы затем к нему вернуться.
*
* @version 1.0
* @author creocoder <creocoder@gmail.com>
*/

class CSetReturnUrlFilter extends CFilter
{
	protected function preFilter($filterChain)
	{
		$app=Yii::app();
		$app->getUser()->setReturnUrl($app->getRequest()->getUrl());

		return true;
	}
}