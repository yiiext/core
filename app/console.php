<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);

// подключаем файл инициализации Yii
require_once(dirname(__FILE__).'/../yii/framework/yii.php');

// файл конфигурации будет отдельный
$configFile=dirname(__FILE__).'/config/console.php';

// создаем и запускаем экземпляр приложения
Yii::createConsoleApplication($configFile)->run();