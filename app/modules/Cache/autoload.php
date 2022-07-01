<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

require_once __DIR__ . '/constants.php';

$m = [
	'Cache'				=> __DIR__ . '/Cache.php',
	'MemcachedAdapter'	=> __DIR__ . '/Adapters/Memcached.php',
];

spl_autoload_register(function ($class) use ($m) {
	if (isset($m[$class])) require_once $m[$class];
}, true);