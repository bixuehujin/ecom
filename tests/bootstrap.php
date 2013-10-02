<?php
require __DIR__ . '/../vendor/autoload.php';

$yiit = __DIR__ . '/../vendor/yiisoft/yii/framework/yiit.php';
$config = __DIR__ . '/config.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_TESTING') or define('YII_TESTING', true);

require_once($yiit);

date_default_timezone_set('UTC');

$command = sprintf('php -S %s:%d -t %s >/dev/null 2>&1 & echo $!', WEB_SERVER_HOST, WEB_SERVER_PORT, WEB_SERVER_DOCROOT);

$output = array();
exec($command, $output);
$pid = (int) $output[0];

echo sprintf('%s - Web server started on %s:%d with PID %d', date('r'), WEB_SERVER_HOST, WEB_SERVER_PORT, $pid) . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
	echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
	exec('kill ' . $pid);
});

Yii::createWebApplication($config);
//Yii::createConsoleApplication($config);
