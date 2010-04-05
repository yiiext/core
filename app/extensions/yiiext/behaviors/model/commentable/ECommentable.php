<?php
/**
 * Commentable behaviour
 *
 * Provides ability to manage comments for an AR model.
 *
 * @version 1.0
 * @author Alexander Makarov
 * @link http://code.google.com/p/yiiext/
 *
 * @throws CException
 */
class ECommentable extends CActiveRecordBehavior {
	public $commentModelClass;
	public $ownerIdField = 'owner_id';
	public $parentIdField = 'parent_id';
	

	/**
	 * Add new comment to current post.
	 *
	 * @throws CException
	 * @param ECommentModel $comment Comment object.
	 * @param int $parentId Parent comment id.
	 * @return boolean
	 */
    function addComment(ECommentModel $comment){				
		$comment->setAttribute($this->ownerIdField, $this->getOwner()->getPrimaryKey());		

		return $comment->save();
    }

	/**
	 * Get comments for curent model.
	 * If $parentId is set, only models with parent id specified are returned.
	 *
	 * @param int $parentId
	 * @return CActiveRecord[] Array of comment models.
	 */
    function getComments($parentId = null){
		$comment = $this->getModelInstance();

		$criteria = new CDbCriteria();
		$criteria->compare($this->ownerIdField, $this->getOwner()->getPrimaryKey());

		if($parentId!==null){
			$criteria->compare($this->parentIdField, $parentId);
		}

		return $comment->findAll($criteria);
    }

	function getCommentsCount($parentId = null){
		$comment = $this->getModelInstance();

		$criteria = new CDbCriteria();
		$criteria->order = "id DESC";
		$criteria->compare($this->ownerIdField, $this->getOwner()->getPrimaryKey());

		if($parentId!==null){
			$criteria->compare($this->parentIdField, $parentId);
		}

		return $comment->count($criteria);
	}	

	/**
	 * @return CActiveRecord
	 */
	private function getModelInstance(){
		if(!isset($this->commentModelClass)){
			throw new CException(Yii::t('yiiext', 'commentModelClass should be defined.'));;
		}
        return CActiveRecord::model($this->commentModelClass);
	}

	function afterDelete($event){
		// removing all model's comments
		$comments = $this->getComments();
		foreach($comments as $comment){
			$comment->delete();
		}

		parent::afterDelete($event);
	}
}