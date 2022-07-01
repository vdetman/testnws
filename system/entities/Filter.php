<?php namespace Entity;

use Helper\Text;

class Filter {
	/**
	 * Поля фильтрации
	 * @var array $fields
	 */
	protected $fields;

	/**
	 *
	 * @var integer
	 */
	protected $total = 0;

	/**
	 * Массив, содержащий варианты склонения элементов, в зависимости от кол-ва [one_item, few_items, many_items]
	 * @var array
	 */
	protected $units = [ 'запись', 'записи', 'записей' ];

	public function __construct( $params = [] ) {
		foreach ( $params as $name => $value ) {
			$this->set( $name, $value );
		}
	}

	/**
	 *
	 * @param integer $total
	 */
	public function setTotal( $total ) {
		$this->total = (int) $total;

		return $this;
	}

	/**
	 *
	 * @return integer $this->total
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 *
	 * @param array $units
	 */
	public function setUnits( array $units ) {
		if ( is_array( $units ) && 3 == count( $units ) ) {
			$this->units = $units;
		}

		return $this;
	}

	/**
	 *
	 * @return string $units
	 */
	public function getUnits() {
		return Text::getEnding( $this->total, $this->units[0], $this->units[1], $this->units[2] );
	}

	/**
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set( $name, $value ) {
		$this->fields[ $name ] = $value;

		return $this;
	}

	/**
	 * @param string
	 */
	public function delete($name)
	{
		if (isset($this->fields[$name]))
			unset($this->fields[$name]);
		return $this;
	}

	/**
	 * @param string
	 * @param mixed
	 */
	public function get($name, $default = null)
	{
		return isset($this->fields[$name]) ? $this->fields[$name] : $default;
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function has($name)
	{
		return is_array($this->fields) && array_key_exists($name, $this->fields);
	}

	/**
	 * Возвращаем HASH текущего набора параметров
	 * @return string
	 */
	public function hash()
	{
		$fields = $this->fields ?: [];
		ksort($fields);
		return md5(json_encode($fields, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Возвращаем JSON текущего набора параметров
	 * @return string
	 */
	public function toJson()
	{
		$fields = $this->fields ?: [];
		ksort($fields);
		return json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	/**
	 * Возвращаем текущий набор параметров
	 * @return array
	 */
	public function toArray()
	{
		return $this->fields ?: [];
	}

	/**
	 * Преобразование параметров Page & PerPage в Limit & Offset
	 */
	public function formLimits()
	{
		if ($this->has('page') && $this->has('per_page')) {
			$this->set('limit', intval($this->get('per_page')));
			$this->set('offset', (intval($this->get('page') - 1)) * intval($this->get('per_page')));
		}
	}
}