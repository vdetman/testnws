<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

return [
	'default' => [
		'label'		=> 'default',
		'driver'	=> 'mysql',
		'hostname'	=> _getenv('DB_HOSTNAME'),
		'port'		=> _getenv('DB_PORT', 'int') ?: 3306,
		'database'	=> _getenv('DB_DATABASE'),
		'username'	=> _getenv('DB_USERNAME'),
		'password'	=> _getenv('DB_PASSWORD'),
		'charset'	=> _getenv('DB_CHARSET') ?: 'utf8mb4',
		'dbcollat'	=> _getenv('DB_DBCOLLAT') ?: 'utf8mb4_general_ci',
		'timezone'	=> _getenv('DB_TIMEZONE') ?: 'Europe/Moscow',
		'ssl'		=> _getenv('DB_SSL'),
		'sqlMode'	=> false,
	]
];