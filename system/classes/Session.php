<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Session
{
	private $dataKey	= '_data_';
	private $flashKey	= '_flash_';

	/**
	 * Fetch a specific item from the session array
	 * @param string
	 * @return mixed
	 */
	public function get($item = null)
	{
		$userdata = isset($_SESSION[$this->dataKey]) ? $_SESSION[$this->dataKey] : [];
		return is_null($item) ? $userdata : (isset($userdata[$item]) ? $userdata[$item] : null);
	}

	/**
	 * Add or change data in the "userdata" array
	 * @param array|string
	 * @param mixed
	 */
	public function set($newdata = [], $newval = '')
	{
		$userdata = isset($_SESSION[$this->dataKey]) ? $_SESSION[$this->dataKey] : [];

		if (is_string($newdata))
			$newdata = [$newdata => $newval];

		if (count($newdata) > 0)
			foreach ($newdata as $key => $val)
				$userdata[$key] = $val;

		$_SESSION[$this->dataKey] = $userdata;
	}

	/**
	 * Delete a session variable from the "userdata" array
	 * @param array|string
	 */
	public function unset($newdata = [])
	{
		$userdata = isset($_SESSION[$this->dataKey]) ? $_SESSION[$this->dataKey] : [];

		if (is_string($newdata))
			$newdata = [$newdata];

		if (count($newdata) > 0)
			foreach ($newdata as $val)
				unset($userdata[$val]);
		if($userdata)
			$_SESSION[$this->dataKey] = $userdata;
		else
			unset($_SESSION[$this->dataKey]);
	}
// -END USERDATA

// FLASHDATA

	/**
	 * Add or change flashdata, only available until the next request
	 * @param array|string
	 * @param mixed
	 */
	public function setFlash($newdata = [], $newval = '')
	{
		$flashdata = isset($_SESSION[$this->flashKey]) ? $_SESSION[$this->flashKey] : [];

		if (is_string($newdata))
			$newdata = [$newdata => $newval];

		if (count($newdata) > 0)
			foreach ($newdata as $key => $val)
				$flashdata[$key] = $val;

		$_SESSION[$this->flashKey] = $flashdata;
	}

	/**
	 * Fetch a specific flashdata item from the session array
	 * @param string
	 * @param boolean
	 * @return mixed
	 */
	public function getFlash($key, $keep = false)
	{
		$flashdata = isset($_SESSION[$this->flashKey]) ? $_SESSION[$this->flashKey] : false;
		if(!$flashdata) return false;
		$flash = isset($flashdata[$key]) ? $flashdata[$key] : false;
		if(!$keep && isset($flashdata[$key])){
			unset($flashdata[$key]);
			if ($flashdata)
				$_SESSION[$this->flashKey] = $flashdata;
			else
				unset($_SESSION[$this->flashKey]);
		}
		return $flash;
	}
// -END FLASHDATA
}