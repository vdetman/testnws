<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

// Debuging
define('VF_SHOW_ERRORS',	true);

// Auxiliary catalogs
define('VF_LOG_DIR',		VF_ROOT_DIR . '/logs');
define('VF_TMP_DIR',		VF_ROOT_DIR . '/_tmp');

// Application
define('VF_APP_DIR',		VF_ROOT_DIR . '/app');
define('VF_MODULES_DIR',	VF_APP_DIR . '/modules');
define('VF_TPLS_DIR',		VF_APP_DIR . '/templates');
define('VF_LIB_DIR',		VF_APP_DIR . '/libraries');

// ENVIRONMENT
$localEnvs = VF_APP_DIR . '/config/environment.php';
if (is_file($localEnvs)) require_once($localEnvs);
define('ENVIRONMENT',	'production' == _getenv('ENVIRONMENT') ? 'production' : 'development');

//Domain
define('PROTOCOL',		getProtocol());
define('SERVER_NAME',	getServerName());
define('DOMAIN',		PROTOCOL . SERVER_NAME);

function _getenv($env = '', $type = false)
{
	$value = getenv($env, true) ?: getenv($env);
	switch ($type) {
		case 'int': $value = intval($value); break;
		case 'float': $value = floatval($value); break;
		case 'bool': case 'boolean': $value = boolval($value); break;
		case 'string': default: $value = strval($value); break;
	}
	return $value;
}

function _isProduction()
{
	return 'production' == ENVIRONMENT;
}

function getServerName()
{
	return trim(filter_input(INPUT_SERVER, 'HTTP_HOST') ?: filter_input(INPUT_SERVER, 'SERVER_NAME'), '/');
}

/**
 * @return uuid
 */
function getTracker()
{
	return filter_input(INPUT_SERVER, 'HTTP_TRACKER')
		?: sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

/**
 * Возвращает разницу между текущим microtime() и переданным
 * @param float
 * @param int
 * @return float
 */
function mtDiff($mt, $precision = 3)
{
	return round(microtime(1) - $mt, $precision);
}

function boolvalue($input)
{
	if (is_bool($input)) return $input;
	if ($input === 0) return false;
	if ($input === 1) return true;
	if (is_string($input)) {
		switch (strtolower($input)) {
			case "true":
			case "on":
			case "1":
				return true;

			case "false":
			case "off":
			case "0":
				return false;
		}
	}
	return null;
}

/** ---------------------------------------- AUTOLOADERS --------------------------------------- */
//Classes / Models
function __autoload_class($class)
{
	// core's classes
	$directory = VF_SYSTEM_DIR . '/classes/';
	if (is_file($directory.$class.'.php')) {
		require_once $directory.$class.'.php';
		return;
	}

	// Controllers
	if ('Controller' == substr($class, -10)) {
		$directory = VF_APP_DIR . '/controllers/';
		if (is_file($directory.$class.'.php')) {
			require_once $directory.$class.'.php';
			return;
		}

		$subControllers = [ADMIN];
		foreach ($subControllers as $subDirectory) {
			if(is_file($directory.$subDirectory.'/'.$class.'.php')){
				require_once($directory.$subDirectory.'/'.$class.'.php');
				return;
			}
		}
	}
}
spl_autoload_register('__autoload_class');
/** --END--------------------------------- AUTOLOADERS --------------------------------------- */

// Fetch the Protocol
function getProtocol()
{
	$p = filter_input(INPUT_SERVER, 'HTTPS') && 'on' == filter_input(INPUT_SERVER, 'HTTPS') ? 'https' : '';
	if (!$p) $p = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_PROTO');
	if (!$p) $p = filter_input(INPUT_SERVER, 'REQUEST_SCHEME');
	return ($p?:'http') . '://';
}

/** -------------------------------------- ERRORS -------------------------------------------- */
$errors = [];
function errorHandler($errno, $error, $file, $line)
{
	// Fix for Exif Data
	if (in_array($errno, [E_WARNING, E_USER_WARNING]) && false !== strpos($error, 'exif_read_data')) return;
	if (in_array($errno, [E_DEPRECATED, E_USER_DEPRECATED])) return;

	global $errors;

	if(!defined('E_STRICT'))			define('E_STRICT', 2048);
	if(!defined('E_RECOVERABLE_ERROR'))	define('E_RECOVERABLE_ERROR', 4096);
	if(!defined('E_DEPRECATED'))		define('E_DEPRECATED', 8192);
	if(!defined('E_USER_DEPRECATED'))	define('E_USER_DEPRECATED', 16384);

	$message = $errno . ' ';
	switch ($errno) {
		case E_USER_ERROR:
			$message = 'ERROR ';
		break;
		case E_WARNING:
		case E_USER_WARNING:
			$message = 'WARNING ';
		break;
		case E_NOTICE:
		case E_USER_NOTICE:
			$message = 'NOTICE ';
		break;
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
			$message = 'DEPRECATED ';
		break;
		case E_RECOVERABLE_ERROR:
			$message = 'E_RECOVERABLE_ERROR ';
			break;
		case E_STRICT:
			$message = 'E_STRICT ';
			break;
	}
	$file = str_replace(VF_ROOT_DIR, '', $file); // Уберем ROOT_DIR из пути к файлу
	$message .= $error.' in '.$file.':'.$line."\n";
	error_log('['.str_pad(getIp() ?: '', 15, ' ', STR_PAD_LEFT).'] ' . date('[H:i:s] ').$message, 3, VF_LOG_DIR . '/core-'.date('Y-m-d').'.log');
	if (ini_get("display_errors") && defined("VF_SHOW_ERRORS") && VF_SHOW_ERRORS)
		$errors[] = $message;
}

// Fetch the IP Address
function getIp()
{
	$ip = '';

	if (false != ($try = filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP'))) {
		$ips = explode(',', $try);
		$ip = isValidIp($ips[0]) ? $ips[0] : '';
	}

	if (!$ip && false != ($try = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'))) {
		$ips = explode(',', $try);
		$ip = isValidIp($ips[0]) ? $ips[0] : '';
	}

	if (!$ip && false != ($try = filter_input(INPUT_SERVER, 'REMOTE_ADDR'))) {
		$ip = isValidIp($try) ? $try : '';
	}

	return $ip;
}

// Is valid Ip address
function isValidIp($ip, $type = 'ipv4')
{
	$type = strtolower($type);
	$flag = '';
	switch ($type) {
		case 'ipv4': $flag = FILTER_FLAG_IPV4; break;
		case 'ipv6': $flag = FILTER_FLAG_IPV6; break;
	}
	return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
}

/**
 * Set HTTP Status Header
*/
function setStatusHeader($code = 200, $text = '')
{
	$stati = array(
		200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
		300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect',
		400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed',
		500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported'
	);

	if(isset($stati[$code]) && $text == '')
		$text = $stati[$code];

	$serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') ?: false;

	if (substr(php_sapi_name(), 0, 3) == 'cgi')
		header("Status: {$code} {$text}", true);
	elseif($serverProtocol == 'HTTP/1.1' || $serverProtocol == 'HTTP/1.0')
		header($serverProtocol." {$code} {$text}", true, $code);
	else
		header("HTTP/1.1 {$code} {$text}", true, $code);
}
set_error_handler("errorHandler");
/** --END--------------------------------- ERRORS -------------------------------------------- */

// init constants
function loadConstants()
{
	$dir = VF_APP_DIR . '/constants/';
		if ($files = scandir($dir)) {
			foreach ($files as $f) {
				if('.php' == substr($f, -4)) require_once($dir . $f);
			}
		}
	unset($dir, $files, $f);
	return;
}
loadConstants();

require_once(VF_SYSTEM_DIR . '/autoload.php');

// Modules
foreach (scandir(VF_MODULES_DIR) as $m) {
	if (in_array($m, ['.','..'])) continue;
	if (file_exists(VF_MODULES_DIR . '/' . ucfirst($m) . '/autoload.php'))
		include_once VF_MODULES_DIR . '/' . ucfirst($m) . '/autoload.php';
}