<div id="welcome">
<h2>Welcome to yiiext</h2>

<p>
yiiext is the unofficial extension repository for the 
<a href="http://www.yiiframework.com/">Yii Framework</a>.
This project was created as an alternative to zii, official extensions repository.
All stable extensions here are compatible with latest release version of Yii.<br/>

If you have any questions have a look at our {CHtml::link('FAQ', ['site/faq'])} page or {CHtml::link('contact us', ['site/contact'])} directly.

</p>

<h2>Browse Extensions</h2>

<div class="filter">
	Category:
	{foreach $this->categories as $cat=>$creg}
		{CHtml::link(
			ucfirst($cat), 
			['site/index', 'category'=>$cat],
			['style'=>($category==$cat) ? 'font-weight: bold;' : 'font-weight: normal;']
		)}{if !$creg@last}, {/if}
    {/foreach}<br />
</div>

{$this->widget('zii.widgets.CListView', [
	'dataProvider'=>$repos,
	'itemView'=>'_repo',
	'template'=>'{sorter}{pager}<br />{items}<br style="clear: both;" />{pager}',
	'sortableAttributes'=>[
        'name'=>'name',
		'created_at'=>'age',
		'pushed_at'=>'last commits',
		'watchers'=>'popularity'
    	]
], true)}

</div>
<br style="clear: both;" />
