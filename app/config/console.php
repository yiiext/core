<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'yiiext console',	

    // preloading 'log' component
    'preload'=>array('log'),

	'import'=>array(
        'application.models.*',
		'application.components.*',
	),

	'components'=>array(
        'db'=>array(
            'class'=>'system.db.CDbConnection',
            'connectionString'=>'mysql:host=localhost;dbname=yiitest',
            'username'=>'root',
            'password'=>'',
            'charset'=>'utf8',

            'emulatePrepare'=>true,

            'enableProfiling'=>true,
            'enableParamLogging' => true,
        ),

        'cache' => array(
            'class' => 'CFileCache',
        ),

        'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'logFile'=>'console.log',
					'levels'=>'error, warning',
				),
				array(
					'class'=>'CFileLogRoute',
					'logFile'=>'console_trace.log',
					'levels'=>'trace',
				),
			),
		),
	),
);
