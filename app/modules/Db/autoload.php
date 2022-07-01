<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

require_once __DIR__ . '/constants.php';

$m = [
	'Db'		=> __DIR__ . '/Db.php',
	'Db\Params'	=> __DIR__ . '/Params.php',
];

spl_autoload_register(function ($class) use ($m) {
	if (isset($m[$class])) require_once $m[$class];
}, true);