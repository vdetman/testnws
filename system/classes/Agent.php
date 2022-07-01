<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Agent extends Core
{
	var $agent		= null;

	var $is_browser	= false;
	var $is_robot	= false;
	var $is_mobile	= false;

	var $languages	= [];
	var $charsets	= [];

	var $platforms	= [];
	var $browsers	= [];
	var $mobiles	= [];
	var $robots		= [];

	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $mobile		= '';
	var $robot		= '';

	public function __construct()
	{
		$this->agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT') ?: null;

		if(!is_null($this->agent))
			if($this->_load_agent_file())
				$this->_compile_data();
	}

	//Compile the User Agent Data
	private function _load_agent_file()
	{
		$data = Config::load('agents');

		$return = false;

		if(isset($data['platforms'])){
			$this->platforms = $data['platforms'];
			unset($data['platforms']);
			$return = true;
		}

		if (isset($data['browsers'])){
			$this->browsers = $data['browsers'];
			unset($data['browsers']);
			$return = true;
		}

		if (isset($data['mobiles'])){
			$this->mobiles = $data['mobiles'];
			unset($data['mobiles']);
			$return = true;
		}

		if (isset($data['robots'])){
			$this->robots = $data['robots'];
			unset($data['robots']);
			$return = true;
		}

		return $return;
	}

	//Compile the User Agent Data
	private function _compile_data()
	{
		$this->_set_platform();
		foreach (array('_set_robot', '_set_browser', '_set_mobile') as $function)
			if ($this->$function() === true)
				break;
	}

	//Set the Platform
	private function _set_platform()
	{
		if (is_array($this->platforms) && count($this->platforms) > 0){
			foreach ($this->platforms as $key => $val){
				if (preg_match("|".preg_quote($key)."|i", $this->agent)){
					$this->platform = $val;
					return true;
				}
			}
		}
		$this->platform = 'Unknown Platform';
	}

	//Set the Browser
	private function _set_browser()
	{
		if (is_array($this->browsers) && count($this->browsers) > 0){
			foreach ($this->browsers as $key => $val){
				if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $this->agent, $match)){
					$this->is_browser = true;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return true;
				}
			}
		}
		return false;
	}

	//Set the Robot
	private function _set_robot()
	{
		if (is_array($this->robots) && count($this->robots) > 0){
			foreach ($this->robots as $key => $val){
				if (preg_match("|".preg_quote($key)."|i", $this->agent)){
					$this->is_robot = true;
					$this->robot = $val;
					return true;
				}
			}
		}
		return false;
	}

	//Set the Mobile Device
	private function _set_mobile()
	{
		if (is_array($this->mobiles) && count($this->mobiles) > 0){
			foreach ($this->mobiles as $key => $val){
				if (false !== (strpos(strtolower($this->agent), $key))){
					$this->is_mobile = true;
					$this->mobile = $val;
					return true;
				}
			}
		}
		return false;
	}

	//Set the accepted languages
	private function _set_languages()
	{
		if ((count($this->languages) == 0) && filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')){
			$languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'))));
			$this->languages = explode(',', $languages);
		}

		if (count($this->languages) == 0)
			$this->languages = array('Undefined');
	}

	//Set the accepted character sets
	private function _set_charsets()
	{
		if ((count($this->charsets) == 0) && filter_input(INPUT_SERVER, 'HTTP_ACCEPT_CHARSET')){
			$charsets = preg_replace('/(;q=.+)/i', '', strtolower(trim(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_CHARSET'))));
			$this->charsets = explode(',', $charsets);
		}

		if (count($this->charsets) == 0)
			$this->charsets = array('Undefined');
	}

	//Is Browser
	public function is_browser($key = null)
	{
		if (!$this->is_browser) return false;

		// No need to be specific, it's a browser
		if ($key === null) return true;

		// Check for a specific browser
		return array_key_exists($key, $this->browsers) && $this->browser === $this->browsers[$key];
	}

	//Is Robot
	public function is_robot($key = null)
	{
		if (!$this->is_robot) return false;

		// No need to be specific, it's a robot
		if ($key === null) return true;

		// Check for a specific robot
		return array_key_exists($key, $this->robots) && $this->robot === $this->robots[$key];
	}

	//Is Mobile
	public function is_mobile($key = null)
	{
		if (!$this->is_mobile) return false;

		// No need to be specific, it's a mobile
		if ($key === null) return true;

		// Check for a specific robot
		return array_key_exists($key, $this->mobiles) && $this->mobile === $this->mobiles[$key];
	}

	//Is this a referral from another site?
	public function is_referral()
	{
		return filter_input(INPUT_SERVER, 'HTTP_REFERER') ? true : false;
	}

	//Agent String
	public function agent_string()
	{
		return $this->agent;
	}

	//Get Platform
	public function platform()
	{
		return $this->platform;
	}

	//Get Browser Name
	public function browser()
	{
		return $this->browser;
	}

	//Get the Browser Version
	public function version()
	{
		return $this->version;
	}

	//Get The Robot Name
	public function robot()
	{
		return $this->robot;
	}

	//Get the Mobile Device
	public function mobile()
	{
		return $this->mobile;
	}

	//Get the referrer
	public function referrer()
	{
		return filter_input(INPUT_SERVER, 'HTTP_REFERER') ?: '';
	}

	//Get the accepted languages
	public function languages()
	{
		if (count($this->languages) == 0)
			$this->_set_languages();
		return $this->languages;
	}

	//Get the accepted Character Sets
	public function charsets()
	{
		if (count($this->charsets) == 0)
			$this->_set_charsets();
		return $this->charsets;
	}

	//Test for a particular language
	public function accept_lang($lang = 'en')
	{
		return (in_array(strtolower($lang), $this->languages(), true));
	}

	//Test for a particular character set
	public function accept_charset($charset = 'utf-8')
	{
		return (in_array(strtolower($charset), $this->charsets(), true));
	}
}