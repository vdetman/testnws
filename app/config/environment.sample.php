<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

/**
 * Вы можете СКОПИРОВАТЬ этот файл в environment.php
 * В новом environment.php раскомментируйте нужные элементы
 * и пропишите им свои значения
 * !!! Не удаляйте этот файл и не меняйте значения тут,
 * это не сработает
 */

$__env = [

	//'ENVIRONMENT' => 'development',

	//'CACHE_ENABLED'	=> false,
	//'CACHE_PREFIX'	=> 'Cache_Prefix',
	//'CACHE_HOST'		=> '127.0.0.1',
	//'CACHE_PORT'		=> 11211,

	//'DB_HOSTNAME'	=> 'localhost',
	//'DB_DATABASE'	=> 'db_base',
	//'DB_USERNAME'	=> 'db_user',
	//'DB_PASSWORD'	=> 'db_pass',
	//'DB_PORT'		=> 3306,
	//'DB_SSL'		=> false,
];

foreach ($__env as $k => $v)
	putenv("{$k}={$v}");
