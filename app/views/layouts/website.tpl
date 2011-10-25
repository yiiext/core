<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
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

<div class="container" id="page">

	<div id="header">
		<div id="logo">
            <h1>{CHtml::encode(Yii::app()->name)}</h1>
            unofficial yii extension repository
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