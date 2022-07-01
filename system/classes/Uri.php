<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Uri extends Core
{
	// URI parts
	protected $scheme;
	protected $host;
	protected $port;
	protected $user;
	protected $pass;
	protected $path;
	protected $query;
	protected $fragment;

	/**
	 * @var string $permittedUriChars
	 */
	protected $permittedUriChars = 'a-z0-9~%.:_\-/=&*?+#;,[]';

	/**
	 * @var string $request
	 */
	protected $request;

	/**
	 * @var array $segments
	 */
	protected $segments = [];

	function __construct($uri = null)
	{
		// Current OR Passed URL
		$uri = $uri ? trim($uri) : $this->_request();

		// Filtering
		$uri = $this->_filter($uri);

		// Check URL
		$this->_check($uri);

		// Parse URL
		$this->_parse($uri);
	}

	/**
	* Test to see if a request was made from the command line.
	* @return string
	*/
	private function _request()
	{
		if (PHP_SAPI === 'cli' OR defined('STDIN')) { // Is CLI?
			$args = array_slice($_SERVER['argv'], 1);
			return $args ? implode('/', $args) : '';
		} else {
			return trim(filter_input(INPUT_SERVER, 'REQUEST_URI'));
		}
	}

	public function getScheme() {
		return $this->scheme;
	}

	public function getHost() {
		return $this->host;
	}

	public function getPort() {
		return $this->port;
	}

	public function getUser() {
		return $this->user;
	}

	public function getPass() {
		return $this->pass;
	}

	public function getPath() {
		return $this->path;
	}

	public function getQuery() {
		return $this->query;
	}

	public function getFragment() {
		return $this->fragment;
	}

	public function getRequest() {
		return $this->request;
	}

	public function getSegments($part = null) {
		if (null === $part)
			return $this->segments;

		return !empty($this->segments[$part]) ? $this->segments[$part] : null;
	}

	/**
	 * Parsing of URI string
	 * @params string $uri
	 * @return void
	 */
	private function _parse($uri)
	{
		if (!empty($uri)) $this->request = $uri;

		$parts = parse_url($uri);

		if (!empty($parts['scheme'])) $this->scheme = $parts['scheme'];
		if (!empty($parts['host'])) $this->host = $parts['host'];
		if (!empty($parts['port'])) $this->port = $parts['port'];
		if (!empty($parts['user'])) $this->user = $parts['user'];
		if (!empty($parts['pass'])) $this->pass = $parts['pass'];
		if (!empty($parts['path'])) $this->path = $parts['path'];
		if (!empty($parts['query'])) $this->query = $parts['query'];
		if (!empty($parts['fragment'])) $this->fragment = $parts['fragment'];

		$this->segments = array_map('trim', explode('/', trim($this->getPath(), '/')));
	}

	/**
	 * Check the URL for permitted characters
	 * @param string
	 * @return bool
	 * @throws Exception
	 */
	private function _check($uri)
	{
		if ($uri != '' && $this->permittedUriChars != '') {
			if (!preg_match("|^[".str_replace(['\\-', '\-'], '-', preg_quote($this->permittedUriChars, '-'))."]+$|i", $uri)) {
				throw new Exception ('The URI you passed has disallowed characters: ' . $uri);
			}
		}
		return true;
	}

	/**
	 * Filter segments for malicious characters
	 * @param string
	 * @return string
	 */
	private function _filter($uri)
	{
		return strtr($uri, [ '$' => '&#36;', '(' => '&#40;', ')' => '&#41;', '%28' => '&#40;', '%29' => '&#41;']);
	}
}