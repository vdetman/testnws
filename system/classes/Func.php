<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

/**
 * Библиотека основных функций
 */
class Func
{
	/**
	 * Определяем время изменения файла и формируем метку
	 * @param mixed $publicPath
	 * @return string
	 */
	static function withModifyTime($publicPath = '')
	{
		return $publicPath . (file_exists(VF_PUBLIC_DIR.'/'.$publicPath) ? '?mod='.(filemtime(VF_PUBLIC_DIR.'/'.$publicPath)) : '');
	}

	static function redirect($uri = '', $httpResponseCode = 302)
	{
		$uri = preg_match('#^https?://#i', $uri) ? $uri : DOMAIN . '/'. ltrim($uri, '/');
		header("Location: ".$uri, true, $httpResponseCode);
		die();
	}
}