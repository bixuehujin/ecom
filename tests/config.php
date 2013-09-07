<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Yii-common testing application',
	'timeZone' => 'Asia/Shanghai',
	// preloading 'log' component
	'preload'=>array('log'),
	'aliases' => array(
		'common' => __DIR__ . '/../../'
	),
	'import'=>array(
		'common.models.*',
		'common.components.*',
	),
	// application components
	'components'=>array(
		'common'=>array(
			'class'=>'common.components.Common',
		),
		'fileManager'=>array(
			'class'=>'common.components.FileManager',
			'basePath' => 'files',
			'domains' => array(
				'pictures' => 'pictures',
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=test',
			'emulatePrepare' => true,
			'username' => 'test',
			'password' => 'test',
			'charset' => 'utf8',
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
