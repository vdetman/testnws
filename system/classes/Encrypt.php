<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Encrypt extends Core
{
	var $encryption_key	= '';
	var $_hash_type	= 'sha1';
	var $_mcrypt_exists = false;
	var $_mcrypt_cipher;
	var $_mcrypt_mode;

	public function __construct()
	{
		$this->_mcrypt_exists = function_exists('mcrypt_encrypt');
	}

	//Fetch the encryption key
	function get_key($key = '')
	{
		if ($key == '')
		{
			if ($this->encryption_key != '')
				return $this->encryption_key;

			$key = ENCRYPTION_KEY;

			if ($key == false)
				throw new Exception('In order to use the encryption class requires that you set an encryption key in your config file.');
		}
		return md5($key);
	}

	//Set the encryption key
	function set_key($key = '')
	{
		$this->encryption_key = $key;
	}

	//Encode
	function encode($string, $key = '')
	{
		$key = $this->get_key($key);
		$enc = $this->_mcrypt_exists === true ? $this->mcrypt_encode($string, $key) : $this->_xor_encode($string, $key);
		return base64_encode($enc);
	}

	//Decode
	function decode($string, $key = '')
	{
		$key = $this->get_key($key);

		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
			return false;

		$dec = base64_decode($string);

		if ($this->_mcrypt_exists === true){
			if (($dec = $this->mcrypt_decode($dec, $key)) === false)
				return false;
		}else{
			$dec = $this->_xor_decode($dec, $key);
		}

		return $dec;
	}

	//Encode from Legacy
	function encode_from_legacy($string, $legacy_mode = MCRYPT_MODE_ECB, $key = '')
	{
		if ($this->_mcrypt_exists === false){
			trigger_error('Encoding from legacy is available only when Mcrypt is in use', E_USER_ERROR);
			return false;
		}

		// decode it first
		// set mode temporarily to what it was when string was encoded with the legacy
		// algorithm - typically MCRYPT_MODE_ECB
		$current_mode = $this->_get_mode();
		$this->set_mode($legacy_mode);

		$key = $this->get_key($key);

		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
			return false;

		$dec = base64_decode($string);

		if (($dec = $this->mcrypt_decode($dec, $key)) === false)
			return false;

		$dec = $this->_xor_decode($dec, $key);

		// set the mcrypt mode back to what it should be, typically MCRYPT_MODE_CBC
		$this->set_mode($current_mode);

		// and re-encode
		return base64_encode($this->mcrypt_encode($dec, $key));
	}

	//XOR Encode
	function _xor_encode($string, $key)
	{
		$rand = '';
		while (strlen($rand) < 32){
			$rand .= mt_rand(0, mt_getrandmax());
		}

		$rand = $this->hash($rand);

		$enc = '';
		for ($i = 0; $i < strlen($string); $i++){
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
		}

		return $this->_xor_merge($enc, $key);
	}

	//XOR Decode
	function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++){
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
		}
		return $dec;
	}

	//XOR key + string Combiner
	function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++){
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}
		return $str;
	}

	//Encrypt using Mcrypt
	function mcrypt_encode($data, $key)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
	}

	//Decrypt using Mcrypt
	function mcrypt_decode($data, $key)
	{
		$data = $this->_remove_cipher_noise($data, $key);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());

		if ($init_size > strlen($data))
			return false;

		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
	}

	//Adds permuted noise to the IV + encrypted data to protect
	//against Man-in-the-middle attacks on CBC mode ciphers
	function _add_cipher_noise($data, $key)
	{
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';

		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j){
			if ($j >= $keylen) $j = 0;
			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}

		return $str;
	}

	//Removes permuted noise from the IV + encrypted data, reversing
	function _remove_cipher_noise($data, $key)
	{
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';

		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j){
			if ($j >= $keylen) $j = 0;
			$temp = ord($data[$i]) - ord($keyhash[$j]);
			if ($temp < 0) $temp = $temp + 256;
			$str .= chr($temp);
		}

		return $str;
	}

	//Set the Mcrypt Cipher
	function set_cipher($cipher)
	{
		$this->_mcrypt_cipher = $cipher;
	}

	//Set the Mcrypt Mode
	function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
	}

	//Get Mcrypt cipher Value
	function _get_cipher()
	{
		if ($this->_mcrypt_cipher == '')
			$this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;

		return $this->_mcrypt_cipher;
	}

	//Get Mcrypt Mode Value
	function _get_mode()
	{
		if ($this->_mcrypt_mode == '')
			$this->_mcrypt_mode = MCRYPT_MODE_CBC;

		return $this->_mcrypt_mode;
	}

	//Set the Hash type
	function set_hash($type = 'sha1')
	{
		$this->_hash_type = ($type != 'sha1' AND $type != 'md5') ? 'sha1' : $type;
	}

	//Hash encode a string
	function hash($str)
	{
		return ($this->_hash_type == 'sha1') ? $this->sha1($str) : md5($str);
	}

	//Generate an SHA1 Hash
	function sha1($str)
	{
		if(!function_exists('sha1')){
			if(!function_exists('mhash')){
				require_once(VF_SYSTEM_DIR . '/classes/Sha1.php');
				$SH = new SHA;
				return $SH->generate($str);
			}else{
				return bin2hex(mhash(MHASH_SHA1, $str));
			}
		}else{
			return sha1($str);
		}
	}

// Custom methods
	/**
	 * @param string
	 * @return string
	 */
	public function enc($string)
	{
		if (!mb_strlen($string)) return '';
		$key = $this->_key();
		$keyRev = $this->_keyRev($key);
		$step_1 = implode('~', [$string, $key]);
		$step_2 = base64_encode($step_1 . $keyRev);
		$step_3 = $keyRev . base64_encode($step_2);
		$step_4 = base64_encode($step_3);
		return $step_4;
	}

	/**
	 * @param string
	 * @return string
	 */
	public function dec($string)
	{
		if (!mb_strlen($string)) return '';
		$key = $this->_key();
		$keyRev = $this->_keyRev($key);
		$step_1 = base64_decode($string); // unstep_4
		$step_2 = base64_decode(substr($step_1, strlen($keyRev))); // unstep_3
		$step_3 = base64_decode($step_2); // unstep_2
		$step_3 = substr($step_3, 0, strlen($step_3) - strlen($keyRev)); // unstep_2
		$step_4 = explode('~', $step_3);
		return isset($step_4[0]) ? $step_4[0] : '';
	}

	/**
	 * @return string
	 */
	private function _key()
	{
		return md5(defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'nFg~hg#sd!br#etb$vn');
	}

	/**
	 * @param string
	 * @return string
	 */
	private function _keyRev($key)
	{
		$modKey = '';
		for($i = 0; $i < strlen($key); $i++) {
			$letter = substr($key, $i, 1);
			$modKey .= in_array($i, [0,3,6,9,11,17,21,28,30]) ? strtoupper($letter) : $letter;
		}
		return strrev($modKey);
	}
}