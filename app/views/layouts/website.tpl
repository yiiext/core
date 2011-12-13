<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/main.css" />
	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/web.css" />
	<link rel="stylesheet" type="text/css" href="{Yii::app()->request->baseUrl}/css/form.css" />

	<title>{CHtml::encode($this->pageTitle)}</title>
</head>

<body>

<a href="{$this->repoUrl|default:'https://github.com/yiiext'}"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/7afbc8b248c68eb468279e8c17986ad46549fb71/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub"></a>

<div class="container" id="page">

	<div id="header">
        <div class="extensions-count"><b>{$this->api->repoCount}</b> great Yii extensions!</div>
		<div id="logo">
            <h1>{CHtml::encode(Yii::app()->name)}</h1>
            unofficial Yii extension repository
        </div>
	</div><!-- header -->

	<div id="mainmenu">
		{$this->widget('zii.widgets.CMenu', [
			'items'=>[
				['label'=>'Extensions', 'url'=>['/site/index']],
				['label'=>'FAQ', 'url'=>['/site/faq']],
				['label'=>'Contact', 'url'=>['/site/contact']]
			]
		], true)}
	</div><!-- mainmenu -->

    <div id="content">
        {$content}
    </div>

    <div id="footer">
        {Yii::powered()}
    </div><!-- footer -->

</div><!-- page -->
</body>
</html>
