<?php
/**
 * Unit testing configuration
 */
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'language' => 'ru',
    'name' => 'yiiext',


	// preloading 'log' component
	'preload' => array('log'),
	'import' => array(
		'application.models.*',
		'application.components.*',
		'ext.yiiext.components.shoppingCart.*',
	),
	'components' => array(
		'fixture' => array(
			'class' => 'system.test.CDbFixtureManager',
		),
		'urlManager' => array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules' => include dirname(__FILE__) . '/routes.php',
		),
		'db' => require_once('db.php'),
		'cache' => array(
			'class' => 'CFileCache',
		),
        'viewRenderer'=>array(
            'class'=>'ext.yiiext.renderers.smarty.ESmartyViewRenderer',
            'fileExtension' => '.tpl',
            //'pluginsDir' => 'application.smartyPlugins',
            //'configDir' => 'application.smartyConfig',
        ),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
);
