<?php
/**
 * 
 */
class ECommentsWidget extends CWidget {
	public $model;
	public $comments = array();
	public $owner;

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
		$this->saveComment($model);

		echo CHtml::tag("h2", array(), 'Comments');

		if($this->formBeforeComments){
			echo CHtml::tag("h3", array(), 'Post new comment');

			if(Yii::app()->user->checkAccess('postComments')){
				$this->renderForm($model);
			}
		}

		$this->renderComments($this->comments);

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
			'owner' => $this->owner
		));
	}	

	protected function saveComment($model){
		if(isset($_POST['Comment']) && Yii::app()->user->checkAccess('postComments')){
			$model->attributes = $_POST['Comment'];
			//$parentId = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
			if($this->owner->addComment($model)){
				$this->getController()->redirect(array('question/view', 'id' => $this->owner->getPrimaryKey()));				
			}
		}
	}
}