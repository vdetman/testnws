<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Cache
{
	private $inited;
	private $config;
	private $adapter;
	private $prefix;
	private $tagskey;
	private $expires = 60;

	public function has($key)
	{
		if (is_null($this->getAdapter())) return false;
		return $this->getAdapter()->has($this->prefix . $key);
	}

	public function get($key)
	{
		if (is_null($this->getAdapter())) return false;
		return $this->getAdapter()->get($this->prefix . $key);
	}

	/**
	 * Установка ключ-значение
	 * @param string $key
	 * @param mixed $value
	 * @param int $expiration
	 * @param string|array $tag
	 * @return boolean|$this
	 */
	public function set($key, $value, $expiration = false, $tag = false)
	{
		if (is_null($this->getAdapter())) return false;
		$expiration = is_int($expiration) ? $expiration : $this->expires;
		$this->getAdapter()->set($this->prefix . $key, $value, $expiration);

		if ($tag) {
			$this->_addToTags($tag, $key);
		}

		return $this;
	}

	public function delete($key)
	{
		if (is_null($this->getAdapter())) return false;
		$this->getAdapter()->delete($this->prefix . $key);
		$this->_removeFromTags($key);
		return true;
	}

	public function flush()
	{
		if (is_null($this->getAdapter())) return false;
		return $this->getAdapter()->flush();
	}

	public function clear()
	{
		if (is_null($this->getAdapter())) return false;
		foreach ($this->getAllKeys() as $key) {
			if (!$this->getAdapter()->delete($key)) {
				return false;
			}
		}
		return true;
	}

	public function deleteByTag($tag)
	{
		if (is_null($this->getAdapter())) return false;
		$existTags = $this->getAllTags();
		if(!isset($existTags[$tag])) return false;
		foreach ($existTags[$tag] as $key) {
			$this->delete($key);
		}
		unset($existTags[$tag]);
		$this->set($this->tagskey, $existTags, 0);
		return true;
	}

	public function getAllTags()
	{
		if (is_null($this->getAdapter())) return false;
		return $this->get($this->tagskey);
	}

	/**
	 * Добавляем тег к ключу
	 * @param string|array $tag
	 * @param string $key
	 * @return boolean
	 */
	private function _addToTags($tag, $key)
	{
		if (is_null($this->getAdapter())) return false;

		// Если передан массив тегов, то прогоним каждый по отдельности
		if (is_array($tag)) {
			foreach ($tag as $tag_item)
				$this->_addToTags($tag_item, $key);
			return;
		} else if (is_scalar($tag)) {

			$tag = strval($tag);

			$existTags = $this->getAllTags();

			if (!$existTags || !array_key_exists($tag, $existTags)) {
				$existTags[$tag] = [];
			}

			$existTags[$tag][] = $key;
			$existTags[$tag] = array_unique($existTags[$tag]);

			$this->set($this->tagskey, $existTags, 0);
		}

		return true;
	}

	/**
	 * Убираем ключ из всех тегов
	 * @param string $key
	 * @return boolean
	 */
	private function _removeFromTags($key)
	{
		if (is_null($this->getAdapter())) return false;

		$key = strval($key);

		$existTags = $this->getAllTags();

		if ($existTags && is_array($existTags)) {
			foreach ($existTags as $tag => $tagKeys) {

				// Прогоняем ключи на поиск удаляемого
				foreach ($tagKeys as $index => $tagKey) {
					if ($key == $tagKey) unset($tagKeys[$index]);
				}

				// Если у данного тега нет ключей, то удалим его полностью
				if (0 == count($tagKeys))
					unset($existTags[$tag]);
				else
					$existTags[$tag] = $tagKeys;
			}
		}

		$this->set($this->tagskey, $existTags, 0);

		return true;
	}

	public function getAllKeys()
	{
		if (is_null($this->getAdapter())) return false;
		$allKeys = [];
		foreach ($this->getAdapter()->getAllKeys() as $key) {
			if ($this->prefix == substr($key, 0, mb_strlen($this->prefix))) {
				$allKeys[] = $key;
			}
		}
		return $allKeys;
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ INITIATION ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
	private function _init()
	{
		if ($this->inited) return true;
		$this->inited = true;

		if (is_null($this->config)) {
			$config = [];
			if (file_exists(__DIR__ . '/config.php'))
				$config = include __DIR__ . '/config.php';
			$this->config = $config;
		}

		if (!$this->config || !$this->config['enabled']) return false;

		if (empty($this->config['adapter'])) return false;

		$className = ucfirst($this->config['adapter']) . 'Adapter';
		if (!class_exists($className)) return false;

		$params = !empty($this->config[$this->config['adapter']]) ? $this->config[$this->config['adapter']] : false;
		if (!$params) return false;

		$adapter = new $className($params);
		$this->_setAdapter($adapter);

		$this->config['prefix'] = ($this->config['prefix'] ?: filter_input(INPUT_SERVER, 'SERVER_NAME')) . ':';
		$this->prefix = $this->config['prefix'];

		$this->config['keytags'] = $this->config['keytags'] ?: 'CacheStorageTags';
		$this->tagskey = $this->config['keytags'];

		$this->config['expires'] = !is_null($this->config['expires']) ? intval($this->config['expires']) : $this->expires;
		$this->expires = $this->config['expires'];

		return true;
	}

	public function config()
	{
		$this->_init();
		return $this->config;
	}

	private function getAdapter()
	{
		$this->_init();
		return $this->adapter;
	}

	private function _setAdapter($adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}

	public function __destruct()
	{
		if (is_null($this->getAdapter())) return false;
		$this->getAdapter()->close();
	}
}