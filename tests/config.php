<?php
return array(
	'basePath'=>__DIR__,
	'name'=>'Yii-common testing application',
	'timeZone' => 'Asia/Shanghai',
	// preloading 'log' component
	'preload'=>array('log'),
	'aliases' => array(
		'common' => __DIR__ . '/../',
		'ecom' => __DIR__ . '/../ecom'
	),
	'import'=>array(
		'common.models.*',
		'common.components.*',
		'common.tests.models.*',
		'common.tests.controllers.*',
	),
	'controllerMap'=>array(
		'tree' => 'common.tests.controllers.TreeController'
	),
	// application components
	'components'=>array(
		'common'=>array(
			'class'=>'common.components.Common',
		),
		'fileManager' => array(
			'class' => 'ecom\file\FileManager',
			'basePath' => __DIR__ . '/file',
			'domains' => array(
				'avatar' => array(
					
				),
			),
		),
		'urlManager'=>array(
			'urlFormat' => 'path',
			'showScriptName'=>false,
			'appendParams'=>false,
		),
		'fixture'=>array(
			'class'=>'system.test.CDbFixtureManager',
			'basePath' => __DIR__ . '/fixtures'
		),
		'db'=>array(
			'connectionString' => 'mysql:dbname=yii-common;host=localhost',
			'emulatePrepare' => true,
			'username' => 'test',
			'password' => 'test',
			'charset' => 'utf8',
			'tablePrefix' => '',
		),
		'redis'=>array(
			'class'=>'redis.ARedisConnection',
			'hostname'=>'localhost',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
);
