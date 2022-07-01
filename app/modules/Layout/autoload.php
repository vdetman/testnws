<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

$m = [
	'Layout'					=> __DIR__ . '/Layout.php',
	'Layout\Entity\PageData'	=> __DIR__ . '/Entity/PageData.php',
	'Layout\Entity\CurrentPage' => __DIR__ . '/Entity/CurrentPage.php',
];

spl_autoload_register(function ($class) use ($m) {
	if (isset($m[$class])) require_once $m[$class];
}, true);