{$var1}-{$this->getId()}|{if $level>0}{$this->widget('application.tests.views.TemplateTestWidget', [
	'template'=>$template,
	'level'=>$level
], true)}{/if}|{$var1}+{$this->getId()}
