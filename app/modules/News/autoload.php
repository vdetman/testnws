<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

$m = [
	'News'					=> __DIR__ . '/News.php',
	'News\NewsModel'		=> __DIR__ . '/NewsModel.php',
	'News\Entity\Item'		=> __DIR__ . '/Entity/Item.php',
	'News\Entity\Tree'		=> __DIR__ . '/Entity/Tree.php',
	'News\Entity\Rubric'	=> __DIR__ . '/Entity/Rubric.php',
];

spl_autoload_register(function ($class) use ($m) {
	if (isset($m[$class])) require_once $m[$class];
}, true);
