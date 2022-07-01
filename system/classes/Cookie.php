<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Cookie extends Core
{
	private $config;
	private $_security; //Instance of Security class

	public function __construct()
	{
		$this->config = [
			'prefix'	=> "",
			'domain'	=> "",
			'path'		=> "/",
			'secure'	=> false,
		];
	}

	/**
	 * Fetch value from Cookie array
	 * @param string $field
	 * @param type $xssClean
	 * @return string
	 */
	public function get($field = '', $xssClean = false)
	{
		return $xssClean ? $this->_xssClean(filter_input(INPUT_COOKIE, $field)) : filter_input(INPUT_COOKIE, $field);
	}

	/**
	 * Is exist value in Cookie array?
	 * @param string $field
	 * @return bool
	 */
	public function has($field = '')
	{
		return filter_input(INPUT_COOKIE, $field) ? true : false;
	}

	/**
	 * Add value to Cookie array
	 * @param string $name
	 * @param string $value
	 * @param string $expire in seconds
	 * @param string $path
	 * @param string $domain
	 * @param string $prefix
	 * @param bool $secure
	 */
	public function set($name = '', $value = '', $expire = '', $path = '/', $domain = '', $prefix = '', $secure = false)
	{
		if (is_array($name))
			foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'name'] as $item)
				if(isset($name[$item])) $$item = $name[$item];

		$domain = !$domain && $this->config['domain'] ? $this->config['domain'] : $domain;
		$prefix = !$prefix && $this->config['prefix'] ? $this->config['prefix'] : $prefix;
		$path = $path == '/' && $this->config['path'] != '/' ? $this->config['path'] : $path;
		$secure = !$secure && $this->config['secure'] ? $this->config['secure'] : $secure;

		$expire = is_numeric($expire) ? (($expire > 0) ? time() + $expire : 0) : (time() - 86500);

		setcookie($prefix.$name, $value, $expire, $path, $domain, $secure);
	}

	/**
	 * Delete value from Cookie array
	 * @param string $name
	 */
	public function clear($name = '')
	{
		setcookie($name, '', time() - 31500000, $this->config['path']);
	}

	private function _xssClean($str)
	{
		$this->_security = $this->_security ?: new Security();
		return $this->_security->xss_clean($str);
	}
}