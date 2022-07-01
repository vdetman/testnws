<?php namespace Db;

class Params
{
	/**
	 * @var string
	 */
	protected $label = 'default';

	/**
	 * @var string
	 */
	protected $driver = 'pgsql';

	/**
	 * @var string
	 */
	protected $host = 'localhost';

	/**
	 * @var string
	 */
	protected $port = 5432;

	/**
	 * @var string
	 */
	protected $database = '';

	/**
	 * @var string
	 */
	protected $username = '';

	/**
	 * @var string
	 */
	protected $password = '';

	/**
	 * @var string
	 */
	protected $charset = 'utf8';

	/**
	 * @var string
	 */
	protected $dbcollat = 'utf8_general_ci';

	/**
	 * @var string
	 */
	protected $ssl;

	/**
	 * @var string
	 */
	protected $sqlMode;

	/**
	 * @var string
	 */
	protected $timezone = 'Europe/Moscow';

	public function label() {
		return $this->label;
	}

	public function driver($ucfirst = false) {
		return $ucfirst ? ucfirst($this->driver) : $this->driver;
	}

	public function host() {
		return $this->host;
	}

	public function port() {
		return $this->port;
	}

	public function database() {
		return $this->database;
	}

	public function username() {
		return $this->username;
	}

	public function password() {
		return $this->password;
	}

	public function charset() {
		return $this->charset;
	}

	public function dbcollat() {
		return $this->dbcollat;
	}

	public function ssl() {
		return $this->ssl;
	}

	public function sqlMode() {
		return $this->sqlMode;
	}

	public function timezone() {
		return $this->timezone;
	}

	public function shell() {
		$parts = [];
		if ($this->host()) $parts[] = "-h {$this->host()}";
		if ($this->port()) $parts[] = "-P {$this->port()}";
		if ($this->ssl()) $parts[] = "--ssl";
		if ($this->username()) $parts[] = "-u {$this->username()}";
		if ($this->password()) $parts[] = "-p{$this->password()}";
		if ($this->database()) $parts[] = "{$this->database()}";
		return implode(" ", $parts);
	}

	private function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	private function setDriver($driver) {
		$this->driver = $driver;
		return $this;
	}

	private function setHost($host) {
		$this->host = $host;
		return $this;
	}

	private function setPort($port) {
		$this->port = $port;
		return $this;
	}

	private function setDatabase($database) {
		$this->database = $database;
		return $this;
	}

	private function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	private function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	private function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	private function setDbcollat($dbcollat) {
		$this->dbcollat = $dbcollat;
		return $this;
	}

	private function setSsl($ssl) {
		$this->ssl = $ssl;
		return $this;
	}

	private function setSqlMode($sqlMode) {
		$this->sqlMode = $sqlMode;
		return $this;
	}

	private function setTimezone($timezone) {
		$this->timezone = $timezone;
		return $this;
	}

	public function fromArray(array $data)
	{
		if (!empty($data['label'])) $this->setLabel($data['label']);
		if (!empty($data['driver'])) $this->setDriver($data['driver']);
		if (!empty($data['hostname'])) $this->setHost($data['hostname']);
		if (!empty($data['port'])) $this->setPort($data['port']);
		if (!empty($data['database'])) $this->setDatabase($data['database']);
		if (!empty($data['username'])) $this->setUsername($data['username']);
		if (!empty($data['password'])) $this->setPassword($data['password']);
		if (!empty($data['charset'])) $this->setCharset($data['charset']);
		if (!empty($data['dbcollat'])) $this->setDbcollat($data['dbcollat']);
		if (!empty($data['ssl'])) $this->setSsl($data['ssl']);
		if (!empty($data['sqlMode'])) $this->setSqlMode($data['sqlMode']);
		if (!empty($data['timezone'])) $this->setTimezone($data['timezone']);
		return $this;
	}
}