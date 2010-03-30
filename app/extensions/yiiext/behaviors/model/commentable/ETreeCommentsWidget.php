<?php
/**
 * 
 */
class ETreeCommentsWidget extends ECommentsWidget {	
	public $idFieldName = 'id';
	public $parentIdFieldName = 'parent_id';

	function init(){
		parent::init();
		Yii::app()->clientScript->registerCoreScript('jquery');		
		Yii::app()->clientScript->registerScriptFile(
    		Yii::app()->assetManager->publish(
        		dirname(__FILE__).'/js/commentable_tree.js'
    		)
		);
	}

	function run(){
		$model = new $this->model();
		$this->saveComment($model);

		echo CHtml::tag("h2", array(), 'Comments');

		$comments = $this->getForest($this->comments);
		$this->renderComments($comments);

		echo CHtml::tag("h3", array('class' => 'postNewComment'), 'Post new comment');

		if(Yii::app()->user->checkAccess('postComments')){
			$this->renderForm($model);
		}
	}

	protected function renderComments($comments){
		echo CHtml::tag("ol", array('class' => 'comments'));
		foreach($comments as $comment){
			echo CHtml::tag("li", array(
				'id' => 'comment-'.$comment->id,				
			));
			$this->render('comment', array('comment' => $comment));
			echo CHtml::link('Reply', '', array(
				'class' => 'reply',
				'data-id' => $comment->id,				
			));
			if(!empty($comment->childNodes)){
				$this->renderComments($comment->childNodes);
			}
			echo CHtml::closeTag("li");
		}
		echo CHtml::closeTag("ol");
	}

	/**
     * Converts rowset to the forest.
	 *
	 * @author http://dklab.ru/wsvn/lib/DbSimple/trunk/lib/DbSimple/Generic.php
     *
     * @param array $rows       Two-dimensional array of resulting rows.     
     * @return array            Transformed array (tree).
     */
     protected function getForest($rows){
		$idName = $this->idFieldName;
		$pidName = $this->parentIdFieldName;

        $children = array(); // children of each ID
        $ids = array();

        // Collect who are children of whom.
        foreach ($rows as $i=>$r) {
            $row =& $rows[$i];
            $id = $row->$idName;

            if ($id === null) {
                // Rows without an ID are totally invalid and makes the result tree to
                // be empty (because PARENT_ID = null means "a root of the tree"). So
                // skip them totally.
                continue;
            }

            $pid = $row->$pidName;
            if ($id == $pid) $pid = null;
            $children[$pid][$id] =& $row;
            if (!isset($children[$id])) $children[$id] = array();
            $row->childNodes =& $children[$id];
            $ids[$id] = true;
        }

        // Root elements are elements with non-found PIDs.
        $forest = array();
        foreach ($rows as $i=>$r) {
            $row =& $rows[$i];
            $id = $row->$idName;
            $pid = $row->$pidName;
            if ($pid == $id) $pid = null;
            if (!isset($ids[$pid])){
                $forest[$row->$idName] =& $row;
            }
            //unset($row->$idName);
            //unset($row->$pidName);
        }

        return $forest;
    }	
}