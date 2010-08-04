<!doctype html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta charset="windows-1251" />
	<title><?php echo $title;?></title>
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="content">
		<?php echo $content;?>
		<hr />
		<p style="text-align: right;">© 2009 — <?php echo Yii::app()->dateFormatter->format('yyyy', time());?>, <a href="http://code.google.com/p/yiiext/">yiiext team</a>.</p>
	</div>
</body>
</html>
