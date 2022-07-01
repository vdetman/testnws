<?php namespace Entity;

class Result
{
	/**
	 * Статус
	 * @var bool $status
	 */
	protected $status;

	/**
	 * Сообщение
	 * @var string
	 */
	protected $message;

	/**
	 * Дополнительная инфа
	 * @var mixed
	 */
	protected $info;

	/**
	 * Привязанный объект
	 * @var mixed
	 */
	protected $object;

	/**
	 * Описание ошибки
	 * @var string
	 */
	protected $error;

	/**
	 * Код ошибки
	 * @var int
	 */
	protected $errno;

	/**
	 * Массив сообщений логирования
	 * @var array
	 */
	protected $logs;

	public function getStatus() {
		return $this->status;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getInfo() {
		return $this->info;
	}

	public function getObject() {
		return $this->object;
	}

	public function getError() {
		return $this->error;
	}

	public function getErrno() {
		return $this->errno;
	}

	public function getLogs() {
		return $this->logs;
	}

	/**
	 * @return boolean
	 */
	public function success()
	{
		return $this->status == true;
	}

	/**
	 * @param boolean
	 */
	public function setStatus($status)
	{
		$this->status = boolval($status);
		return $this;
	}

	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	public function setInfo($info) {
		$this->info = $info;
		return $this;
	}

	public function setObject($object) {
		$this->object = $object;
		return $this;
	}

	/**
	 * @param string
	 */
	public function setError($error)
	{
		$this->setStatus(false);
		$this->error = $error;
		return $this;
	}

	/**
	 * @param int
	 */
	public function setErrno($errno)
	{
		$this->setStatus(false);
		$this->errno = $errno;
		return $this;
	}

	/**
	 * @param string
	 */
	public function addLog($log)
	{
		$this->logs[] = $log;
		return $this;
	}
}