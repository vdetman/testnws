<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

return [
	'enabled'	=> _getenv('CACHE_ENABLED', 'boolean'),
	'prefix'	=> _getenv('CACHE_PREFIX'),
	'adapter'	=> 'memcached',
	'expires'	=> 60,
	'keytags'	=> 'CacheStorageTags',
	'memcached'	=> [
		'servers'	=> [
			[
				'host'		=> _getenv('CACHE_HOST'),
				'port'		=> _getenv('CACHE_PORT', 'int'),
				'weight'	=> _getenv('CACHE_WEIGHT', 'int'),
				'sasl'		=> _getenv('CACHE_SASL', 'boolean'),
				'sasl.user'	=> _getenv('CACHE_SASL_PASS'),
				'sasl.pass'	=> _getenv('CACHE_SASL_USER')
			],
		],
	],
];