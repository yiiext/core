<?=CHtml::beginForm('', 'post', array('id' => 'addCommentForm', 'class' => 'addCommentForm'))?>
	<?=CHtml::errorSummary($model)?>
	<?if(!isset($parent_id)) $parent_id = 0?>
	<?=CHtml::activeHiddenField($model, 'parent_id', array('value' => $parent_id, 'id' => 'comment_parent_id'))?>
	<ul>
		<li>
		<?=CHtml::activeTextArea($model, 'text')?>
		</li>
	</ul>

	<?=CHtml::submitButton($model->isNewRecord ? 'Post' : 'Save')?>
<?=CHtml::endForm()?>