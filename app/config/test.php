<?php
/**
 * Unit testing configuration
 */
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'language' => 'ru',

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
        'db' => array(
            'class'=>'system.db.CDbConnection',
            'connectionString'=>'mysql:host=localhost;dbname=yiitest',
            'username'=>'root',
            'password'=>'',
            'charset'=>'utf8',

            'emulatePrepare'=>true,
            'enableParamLogging' => true,
        ),
        'cache' => array(
            'class' => 'CFileCache',
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