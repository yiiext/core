v0.1

Для использования нужно создать папку assets в папке с виджетом, скопировать туда папки css images и js из архива с elfinder.

После этого виджет можно использовать так:

<?php

class SiteController extends Controller
{
    public function actions()
    {
        return array(
// Обработчик сообщений от файл-менеджера
            'fileManager'=>array(
                'class'=>'ext.elfinder.ElFinderAction',
            ),
        );
    }
// ...
}
И в представлении:
<?php
$this->widget('application.my.form.widgets.elfinder.ElFinderWidget',array(
	'lang'=>'ru',
    'url'=>CHtml::normalizeUrl(array('site/fileManager')),
    'editorCallback'=>'js:function(url) {
		var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
//      var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");

        window.opener.CKEDITOR.tools.callFunction(funcNum, url);
        window.close();
    }',
//            'htmlOptions'=>array('style'=>'height:500px'),
));

Внимание, код указан на примере использования с расширением ECKEditor