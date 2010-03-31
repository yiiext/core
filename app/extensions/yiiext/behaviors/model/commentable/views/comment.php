<p>
	<strong><?=CHtml::link(CHtml::encode($comment->author->username), array('user/profile', 'id'=>$comment->author->id))?></strong>

	<?=Yii::app()->dateFormatter->format('dd.MM.yyyy, HH:mm', $comment->created_on)?>				
	<?=CHtml::link('#', '#comment-'.$comment->getPrimaryKey())?>
</p>
<?=$comment->text_html ?>