<?php
$yiit = __DIR__ . '/../vendor/yiisoft/yii/framework/yiit.php';
$config = __DIR__ . '/config.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_TESTING') or define('YII_TESTING', true);

require_once($yiit);

Yii::createWebApplication($config)->run();
