<?php
/**
 * 
 */
class ECommentsWidget extends CWidget {
	public $model;
	public $comments = array();
	public $owner;
	public $useAjax = true;
	public $ajaxUrl = '';
	

	public $formBeforeComments = false;	

	function init(){	

		if(!isset($this->owner)) throw new CException(Yii::t('yiiext', 'owner should be defined.'));
		if(!isset($this->model)) throw new CException(Yii::t('yiiext', 'model should be defined.'));

		Yii::app()->clientScript->registerCssFile(
    		Yii::app()->assetManager->publish(
        		dirname(__FILE__).'/css/commentable.css'
    		)
		);
	}

	function run(){
		$model = new $this->model();		

		echo CHtml::tag("h2", array(), 'Comments');

		if($this->formBeforeComments){
			echo CHtml::tag("h3", array(), 'Post new comment');

			if(Yii::app()->user->checkAccess('postComments')){
				$this->renderForm($model);
			}
		}
		
		echo CHtml::tag("div", array('class' => 'comments-container'));
		$this->renderComments($this->comments);
		echo CHtml::closeTag("div");


		if(!$this->formBeforeComments){
			echo CHtml::tag("h3", array(), 'Post new comment');

			if(Yii::app()->user->checkAccess('postComments')){
				$this->renderForm($model);
			}
		}
	}

	protected function renderComments($comments){
		echo CHtml::tag("ol", array('class' => 'comments'));
		foreach($comments as $comment){
			echo CHtml::tag("li", array(
				'id' => 'comment-'.$comment->getPrimaryKey(),
			));
			$this->render('comment', array('comment' => $comment));
			echo CHtml::closeTag("li");
		}
		echo CHtml::closeTag("ol");
	}	

	protected function renderForm($model){
		$this->render('form', array(
			'model' => $model,						
		));
	}	
}