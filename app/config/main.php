<?php
// This is the main application configuration. Any writable
// application properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'yiiext',
	'defaultController'=>'test',

    // preloading 'log' component
    'preload'=>array('log'),

	'import'=>array(
        'application.models.*',
		'application.components.*',
	),
	'components'=>array(
		'urlManager'=>array(
			'urlFormat'=>'path',
            'rules'=> require(dirname(__FILE__).'/routes.php'),
			'showScriptName' => false,
            //'useStrictParsing' => true,
		),

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
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CProfileLogRoute',

                )
            ),
        ),
	),
);
