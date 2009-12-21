<?php
define('YII_DEBUG', true);
$webRoot=dirname(__FILE__);
//require_once(dirname($webRoot).'/framework/yiilite.php');
require_once(dirname($webRoot).'/framework/yii.php');
$configFile=$webRoot.'/../app/config/main.php';
Yii::createWebApplication($configFile)->run();
