<?php
require __DIR__ . '/../vendor/autoload.php';

$yiit = __DIR__ . '/../vendor/yiisoft/yii/framework/yiit.php';
$config = __DIR__ . '/config.php';

require_once($yiit);

Yii::createConsoleApplication($config);
