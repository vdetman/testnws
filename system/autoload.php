<?php if (!defined('VF_ROOT_DIR')) die('Direct access denied');

$m = [
	// Helpers
	'Helper\Text'		=> __DIR__ . '/helpers/Text.php',
	'Helper\Date'		=> __DIR__ . '/helpers/Date.php',
	'Helper\File'		=> __DIR__ . '/helpers/File.php',
	'Helper\Sql'		=> __DIR__ . '/helpers/Sql.php',

	// Entities
	'Entity\Filter'		=> __DIR__ . '/entities/Filter.php',
	'Entity\Result'		=> __DIR__ . '/entities/Result.php',
	'Entity\DateTime'	=> __DIR__ . '/entities/DateTime.php',
];

spl_autoload_register(function ($class) use ($m) {
	if (isset($m[$class])) require_once $m[$class];
}, true);