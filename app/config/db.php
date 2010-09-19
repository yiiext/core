<?php
// DB connection config
return array(
	'class'=>'system.db.CDbConnection',
	'connectionString'=>'mysql:host=localhost;dbname=yiitest',
	'username'=>'root',
	'password'=>'',
	'charset'=>'utf8',

	'emulatePrepare'=>true,

	'enableProfiling'=>true,
	'enableParamLogging' => true,
);
