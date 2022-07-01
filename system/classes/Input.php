<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Input
{
	public $ip;
	public $agent;
	private $_security; //Instance of Security class

	public function __construct(){}

	//Fetch an item from the GET array
	public function get($field = null, $xssClean = false)
	{
		//Full array
		if (is_null($field)){
			$array = [];
			foreach(array_keys($_GET) as $key)
				$array[$key] = $xssClean ? $this->_xssClean($_GET[$key]) : $_GET[$key];
			return $array;
		}

		if(!isset($_GET[$field])) return null;

		return $xssClean ? $this->_xssClean($_GET[$field]) : $_GET[$field];
	}

	//Fetch an item from the POST array
	public function post($field = null, $xssClean = false)
	{
		//Full array
		if (is_null($field)){
			$array = [];
			foreach(array_keys($_POST) as $key)
				$array[$key] = $xssClean ? $this->_xssClean($_POST[$key]) : $_POST[$key];
			return $array;
		}

		if(!isset($_POST[$field])) return null;

		return $xssClean ? $this->_xssClean($_POST[$field]) : $_POST[$field];
	}

	//Fetch an item from the SERVER array
	public function server($field = null, $xssClean = false)
	{
		//Full array
		if (is_null($field)){
			$array = [];
			foreach(array_keys($_SERVER) as $key)
				$array[$key] = $xssClean ? $this->_xssClean(filter_input(INPUT_SERVER, $key)) : filter_input(INPUT_SERVER, $key);
			return $array;
		}
		return $xssClean ? $this->_xssClean(filter_input(INPUT_SERVER, $field)) : filter_input(INPUT_SERVER, $field);
	}

	//Fetch the IP Address
	public function ip()
	{
		$ip = null;

		if ($this->server('HTTP_X_REAL_IP')) {
			$ips = explode(',', $this->server('HTTP_X_REAL_IP'));
			$ip = $this->isValidIp($ips[0]) ? $ips[0] : null;
		}

		if (!$ip && $this->server('HTTP_X_FORWARDED_FOR')) {
			$ips = explode(',', $this->server('HTTP_X_FORWARDED_FOR'));
			$ip = $this->isValidIp($ips[0]) ? $ips[0] : null;
		}

		if (!$ip && $this->server('REMOTE_ADDR')) {
			$ip = $this->isValidIp($this->server('REMOTE_ADDR')) ? $this->server('REMOTE_ADDR') : null;
		}

		$this->ip = $ip;
		return $this->ip;
	}

	//Is valid Ip address
	public function isValidIp($ip, $type = 'ipv4')
	{
		$type = strtolower($type);
		$flag = '';
		switch ($type) {
			case 'ipv4': $flag = FILTER_FLAG_IPV4; break;
			case 'ipv6': $flag = FILTER_FLAG_IPV6; break;
		}
		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
	}

	//Is ajax Request?
	public function isAjax()
	{
		return (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
	}

	//Is self Request?
	public function isSelf()
	{
		$allowedCodes = ['Ce3oGkfNGqYbcRaSCtT2'];
		$selfCode = $this->get('self', true);
		return in_array($selfCode, $allowedCodes);
	}

	//User Agent
	public function agent()
	{
		$this->agent = $this->agent ?: filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
		return $this->agent;
	}

	private function _xssClean($str)
	{
		$this->_security = $this->_security ?: new Security();
		return $this->_security->xss_clean($str);
	}
}