<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class MemcachedAdapter
{
	protected $adapter;

	public function __construct($params = [])
	{
		if (!extension_loaded('memcached')) return false;

		$a = new Memcached();

		foreach (!empty($params['servers']) ? $params['servers'] : [] as $s) {
			if (!$this->_existServer($a, $s)) {

				$a->addServer($s['host'], $s['port'], $s['weight']);

				if (!empty($params['sasl']))
					$a->setSaslAuthData($params['sasl.user'], $params['sasl.pass']);
			}
		}

		$this->setAdapter($a);
    }

	private function _existServer($a, $s)
	{
		foreach ($a->getServerList() as $sl)
			if ($sl['host'] == $s['host'] && $sl['port'] == $s['port'])
				return true;
		return false;
    }

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAdapter($adapter)
	{
		$this->adapter = $adapter;
	}

	public function has($key)
	{
		return !empty($this->getAdapter()->get($key));
	}

	public function get($key)
	{
		return $this->getAdapter()->get($key);
	}

	public function set($key, $value, $expiration = 0)
	{
		$this->getAdapter()->set($key, $value, $expiration);
		return $value;
	}

	public function delete($key)
	{
		$this->getAdapter()->delete($key);
		return true;
	}

	public function flush()
	{
		return $this->getAdapter()->flush();
	}

	public function close()
	{
		return $this->getAdapter()->quit();
	}

	public function getAllKeys()
	{
		return $this->getAdapter()->getAllKeys();
	}
}