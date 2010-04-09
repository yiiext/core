<?=CHtml::beginForm('', 'post', array('id' => 'addCommentForm', 'class' => 'addCommentForm'))?>
	<?=CHtml::errorSummary($model)?>
	<?if(!isset($parent_id)) $parent_id = 0?>
	<?=CHtml::activeHiddenField($model, $this->owner->parentIdField, array('value' => $parent_id, 'id' => 'comment_parent_id'))?>	
	<?=CHtml::activeHiddenField($model, $this->owner->ownerIdField, array('value' => $this->owner->id))?>
	<ul>
		<li>
		<?=CHtml::activeTextArea($model, 'text')?>
		</li>
	</ul>

	<?if($this->useAjax):?>
		<?=CHtml::ajaxSubmitButton($model->isNewRecord ? 'Post' : 'Save', $this->ajaxUrl, array(
			'update' => '.comments-container',
			'success' => 'function(html){jQuery(".comments-container").html(html); $("#addCommentForm textarea").val("")}',			
		))?>
	<?else:?>
		<?=CHtml::submitButton($model->isNewRecord ? 'Post' : 'Save')?>
	<?endif?>	
<?=CHtml::endForm()?>