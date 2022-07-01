<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Db\Params;

class Db
{
	private $inst;
	private $stmt;
	private $label;
	private $params;
	private $config;
	private $lastQuery;

	public function __construct()
	{
		if (is_null($this->config))
			$this->config = include __DIR__ . '/config.php';
		$this->setConnect(); // default
	}

	/**
	 * @return boolean
	 */
	public function setConnect($label = 'default')
	{
		// If already initialized...
		$this->label = $label;
		if ($this->_inst()) return true;

		// Params
		$par = !empty($this->config[$this->label]) ? (new Params())->fromArray($this->config[$this->label]) : false;
		if (!$par) throw new \Exception("Not found DB params with label {$this->label}");
		$this->params = $par;

		switch ($par->driver()) {
			case 'mysql':
				try {
					$dsn = "mysql:host={$par->host()};dbname={$par->database()};port={$par->port()}";
					if ($par->ssl()) $dsn .= ";sslmode=require";
					$this->_setInst(new PDO($dsn, $par->username(), $par->password()));
					$this->_inst()->exec("SET NAMES {$par->charset()} COLLATE '{$par->dbcollat()}';");
					$this->_inst()->exec("SET GLOBAL time_zone = '{$par->timezone()}';");
					$this->_inst()->exec("SET time_zone='{$par->timezone()}';");
					if ($par->sqlMode()) $this->_inst()->exec("SET SESSION SQL_MODE = '{$par->sqlMode()}';");
					$this->_inst()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				} catch(PDOException $e) {
					$this->_log($e->getMessage());
					throw new \Exception($e->getMessage());
				}
				break;
			case 'pgsql':
				try {
					$dsn = "pgsql:host={$par->host()};dbname={$par->database()};port={$par->port()}";
					if ($par->ssl()) $dsn .= ";sslmode=require";
					$this->_setInst(new PDO($dsn, $par->username(), $par->password()));
					$this->_inst()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				} catch(PDOException $e) {
					$this->_log($e->getMessage());
					throw new \Exception($e->getMessage());
				}
				break;
			default: $this->_log("Unknown driver {$this->label}"); return false;
		}
		return true;
	}

	/**
	 * @return boolean
	 */
	public function ping()
	{
		if (!$this->_inst()) return false;
		$stmt = $this->_inst()->prepare("SELECT 1 AS one");
		$stmt->execute();
		echo 1 == $stmt->columnCount() ? 'Ping: OK' : 'Ping: FAIL';
	}

	/**
	 * @return Params
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ QUERY METHODS BELOW ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
	/**
	 * @param string
	 * @return void
	 */
	public function prepare($query)
	{
		$this->_setStmt($this->_inst()->prepare($query));
	}

	/**
	 * @param array
	 * @return boolean
	 */
	public function execute($data = [])
	{
		if (!$this->stmt()->execute($data)) {
			$this->_log(['query' => $this->stmt()->queryString, 'code' => $this->stmt()->errorCode(), 'error' => $this->stmt()->errorInfo()], 'errors_' . $this->getParams()->label());
			return false;
		}
		return true;
	}

	/**
	 * @param string
	 * @param array
	 * @return boolean
	 */
	public function query($query, $data = [])
	{
		if (!$this->_inst()) return false;
		$this->prepare($query);
		return $this->execute($data);
	}

	/**
	 * @uses by migrations
	 * @param string
	 * @return boolean
	 */
	public function multiQuery($query)
	{
		if (false === $this->_inst()->exec($query)) {
			echo '<pre>',print_r($this->_inst()->errorInfo(), 1),'</pre>'; die();
		}
		return true;
	}

	/**
	 * @param string
	 * @return array
	 */
	public function result($field = null)
	{
		$row = $this->stmt()->fetch(PDO::FETCH_ASSOC);
		if (!is_null($field) && isset($row[$field]))
			return $row[$field];
		elseif (!is_null($field) && !isset($row[$field]))
			return null;
		else
			return $row ?: [];
	}

	/**
	 * @return array
	 */
	public function results()
	{
		return $this->stmt()->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @return int
	 */
	public function lastInsertId()
	{
		return $this->_inst()->lastInsertId();
	}

	/**
	 * @return int
	 */
	public function totalRows()
	{
		$this->query("SELECT found_rows() AS cnt;");
		return $this->result('cnt') ?: 0;
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ PREPARING METHODS BELOW ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
	/**
	 * @param string
	 * @param array
	 * @param boolean
	 * @return string
	 */
	public function phSet($tmpl, $data, $quoteField = false)
	{
		if (empty($tmpl) || empty($data) || false === strpos($tmpl, ":set")) return $tmpl;

		$repl = [];
		foreach ($data as $k => $v) {
			if (is_null($v)) $v = 'NULL';
			elseif (is_bool($v)) $v = $v ? 'true' : 'false';
			elseif (is_int($v) || is_float($v)) $v = str_replace(',', '.', $v);
			else $v = $this->_inst()->quote($v);
			$repl[] = $quoteField ? '"' . $k . '" = '.$v : "{$k} = ".$v;
		}
		return str_replace(':set', implode(', ', $repl), $tmpl);
	}

	/**
	 * @param array
	 * @return string
	 */
	public function phIn($data)
	{
		$repl = [];
		$data = is_array($data) ? $data : [$data];
		foreach (is_array($data) ? ($data ?: [0]) : ($data ? [$data] : [0]) as $v) {
			if (is_null($v)) $v = 'NULL';
			elseif (is_numeric($v) || is_int($v) || is_float($v)) $v = str_replace(',', '.', $v);
			else $v = $this->_inst()->quote($v);
			$repl[] = $v;
		}
		return implode(', ', $repl);
	}

	/**
	 * @param string
	 * @param array
	 * @return string
	 */
	public function phLike($str, $fields)
	{
		$likes = [];
		$str = str_replace("'", "", $str);
		foreach ($fields as $f)
			$likes[] = "{$f} LIKE '%{$str}%'";
		return "(" . implode(' OR ', $likes) . ")";
	}

	/**
	 * @param string
	 * @param array
	 * @param boolean
	 * @return string
	 */
	public function phInsert($tmpl, $data, $quoteField = false)
	{
		if (empty($tmpl) || empty($data) || false === strpos($tmpl, ":insert")) return $tmpl;

		$fields = $values = [];
		foreach ($data as $k => $v) {
			if (is_null($v)) $v = 'NULL';
			elseif (is_int($v) || is_float($v) || is_numeric($v)) $v = str_replace(',', '.', $v);
			else $v = $this->_inst()->quote($v);
			$fields[] = $quoteField ? '"' . $k . '"' : "{$k}";
			$values[] = $v;
		}
		return str_replace(':insert', '(' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')', $tmpl);
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ TRANSACTIONS METHODS BELOW ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

	public function begin()
	{
		return $this->_inst()->beginTransaction();
	}

	public function commit()
	{
		return $this->_inst()->commit();
	}

	public function rollback()
	{
		return $this->_inst()->rollBack();
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ HELPERS | FORMS METHODS BELOW ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
	const KEY_ORDER_BY	= "order_by";
	const KEY_ORDER_DIR	= "order_dir";
	const KEY_GROUP_BY	= "group_by";
	const KEY_LIMIT		= "limit";
	const KEY_OFFSET	= "offset";
	const KEY_PAGE		= "page";
	const KEY_PER_PAGE	= "perPage";

	/**
	 * @param Filter
	 * @param string
	 * @param string
	 * @return string
	 */
	public function _order(Filter $filter, $defaultField = false, $defaultDirection = false)
	{
		$order = "";

		if ($filter->get(self::KEY_ORDER_BY) || $defaultField)
			$order = "ORDER BY " . strval($filter->get(self::KEY_ORDER_BY) ?: $defaultField);

		if ($order && ($filter->has(self::KEY_ORDER_DIR) || in_array(strtolower($defaultDirection), ['asc','desc'])))
			$order .= " " . strval($filter->get(self::KEY_ORDER_DIR) ?: $defaultDirection);

		return $order;
	}

	/**
	 * @param Filter
	 * @return string
	 */
	public function _group(Filter $filter)
	{
		return $filter->get(self::KEY_GROUP_BY) ? "GROUP BY " . strval($filter->get(self::KEY_GROUP_BY)) : "";
	}

	/**
	 * @param Filter
	 * @return string
	 */
	public function _limit(Filter $filter)
	{
		if ($filter->has(self::KEY_LIMIT) && $filter->has(self::KEY_OFFSET))
			return "LIMIT " . intval($filter->get(self::KEY_LIMIT)) . " OFFSET " . intval($filter->get(self::KEY_OFFSET)) . "";
		else if ($filter->has(self::KEY_LIMIT) && !$filter->has(self::KEY_OFFSET))
			return "LIMIT " . intval($filter->get(self::KEY_LIMIT)) . "";
		else if($filter->has(self::KEY_PAGE) && $filter->has(self::KEY_PER_PAGE) && is_numeric($filter->get(self::KEY_PAGE))) {
			$limit = intval($filter->get(self::KEY_PER_PAGE));
			$offset = (intval($filter->get(self::KEY_PAGE)) - 1) * $limit;
			return "LIMIT {$limit} OFFSET {$offset}";
		} else
			return "";
	}

	/**
	 * @param array
	 * @return string
	 */
	public function _where($arWhere = [])
	{
		return $arWhere ? "WHERE ".implode(" AND ", $arWhere) : "";
	}

	/**
	 * @param mixed
	 * @param string
	 * @return boolean
	 */
	protected function _log($log, $filename = '')
	{
		$logDir = VF_LOG_DIR . '/db/';
		if (!is_dir($logDir)) {
			@mkdir($logDir, 0755);
			if (!is_dir($logDir)) return;
		}
		$filepath = $logDir . ($filename ? $filename . '_' : '') . date('Y_m_d') . '.log';

		if (!$fp = @fopen($filepath, 'ab')) return false;

		$message = '['.date('H:i:s').'] ';
		$message .= is_scalar($log) ? $log : (is_array($log) ? json_encode($log, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '');
		$message .= "\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, 0666);

		return true;
	}

	/**
	 * Плейсхолдер для запросов. Пример работы: $query = $db->placehold('SELECT name FROM products WHERE id=?', $id);
	 */
	public function placehold()
	{
		if (!$this->_inst()) return false;

		$args = func_get_args();

		//Шаблон запроса
		$tmpl = array_shift($args);

		//Если переданы аргументы
		if(!empty($args)){
			$result = $this->sql_placeholder_ex($tmpl, $args, $error);
			if ($result === false){
				$error = "Placeholder substitution error. Diagnostics: \"$error\"";
				trigger_error($error, E_USER_WARNING);
				return false;
			}
			return $result;
		}

		//Если аргументов нет, то возвращаем шаблон без изменений
		return $tmpl;
	}

	/**
	 * Выполнение плейсхолдера
	 */
	private function sql_placeholder_ex($tmpl, $args, &$errormsg)
	{
		// Запрос уже разобран?.. Если нет, разбираем.
		$compiled = !is_array($tmpl) ?  $this->sql_compile_placeholder($tmpl) : $tmpl;

		list ($compiled, $tmpl, $has_named) = $compiled;

		// Если есть хотя бы один именованный placeholder, используем
		// первый аргумент в качестве ассоциативного массива.
		if ($has_named)
			$args = @$args[0];

		// Выполняем все замены в цикле.
		$p = 0; // текущее положение в строке
		$out = ''; // результирующая строка
		$error = false; // были ошибки?

		foreach ($compiled as $num=>$e)
		{
			list ($key, $type, $start, $length) = $e;

			// Pre-string.
			$out .= substr($tmpl, $p, $start - $p);
			$p = $start + $length;

			$repl = '';		// текст для замены текущего placeholder-а
			$errmsg = ''; // сообщение об ошибке для этого placeholder-а
			do {
				// Обрабатываем ошибку.
				if (!isset($args[$key]))
				{
					$error = $errmsg = "UNKNOWN_PLACEHOLDER_$key";
					break;
				}
				// Вставляем значение в соответствии с типом placeholder-а.
				$a = $args[$key];
				if ($type === '')
				{
					// Скалярный placeholder.
					if (is_array($a))
					{
						$error = $errmsg = "NOT_A_SCALAR_PLACEHOLDER_$key";
						break;
					}
					$repl = is_int($a) || is_float($a) ? str_replace(',', '.', $a) : "'".$a."'";
					break;
				}
				// Иначе это массив или список.
				if(is_object($a))
					$a = get_object_vars($a);

				if (!is_array($a))
				{
					$error = $errmsg = "NOT_AN_ARRAY_PLACEHOLDER_$key";
					break;
				}
				if ($type === '@')
				{
					// Это список.
					foreach ($a as $v)
					{
						if(is_null($v))
							$r = "NULL";
						else
							$r = is_int($v) || is_float($v) ? $v : "'". $v."'";

						$repl .= ($repl===''? "" : ",").$r;
					}
				}
				elseif ($type === '&')
				{
					// Это набор пар ключ=>значение.
					$lerror = [];
					foreach ($a as $k=>$v)
					{
						if (!is_string($k))
							$lerror[$k] = "NOT_A_STRING_KEY_{$k}_FOR_PLACEHOLDER_$key";
						else
							$k = preg_replace('/[^a-zA-Z0-9_]/', '_', $k);

						if(is_null($v))
							$r = "=NULL";
						else
							$r = "='". $v."'";

						$repl .= ($repl===''? "" : ", ").$k.$r;
					}
					// Если была ошибка, составляем сообщение.
					if (count($lerror))
					{
						$repl = '';
						foreach ($a as $k=>$v)
						{
							if (isset($lerror[$k]))
							{
								$repl .= ($repl===''? "" : ", ").$lerror[$k];
							}
							else
							{
								$k = preg_replace('/[^a-zA-Z0-9_-]/', '_', $k);
								$repl .= ($repl===''? "" : ", ").$k."=?";
							}
						}
						$error = $errmsg = $repl;
					}
				}
			} while (false);
			if ($errmsg) $compiled[$num]['error'] = $errmsg;
			if (!$error) $out .= $repl;
		}
		$out .= substr($tmpl, $p);

		// Если возникла ошибка, переделываем результирующую строку
		// в сообщение об ошибке (расставляем диагностические строки
		// вместо ошибочных placeholder-ов).
		if ($error)
		{
			$out = '';
			$p 	= 0; // текущая позиция
			foreach ($compiled as $num=>$e)
			{
				list ($key, $type, $start, $length) = $e;
				$out .= substr($tmpl, $p, $start - $p);
				$p = $start + $length;
				if (isset($e['error']))
				{
					$out .= $e['error'];
				}
				else
				{
					$out .= substr($tmpl, $start, $length);
				}
			}
			// Последняя часть строки.
			$out .= substr($tmpl, $p);
			$errormsg = $out;
			return false;
		}
		else
		{
			$errormsg = false;
			return $out;
		}
	}

	/**
	 * Компиляция плейсхолдера
	 */
	private function sql_compile_placeholder($tmpl)
	{
		$compiled = [];
		$p = 0;	 // текущая позиция в строке
		$i = 0;	 // счетчик placeholder-ов
		$has_named = false;
		while(false !== ($start = $p = strpos($tmpl, "?", $p)))
		{
			// Определяем тип placeholder-а.
			switch ($c = substr($tmpl, ++$p, 1))
			{
				case '&': case '@': //case '#':
				$type = $c; ++$p; break;
				default:
					$type = ''; break;
			}

			$key = $i;
			$i++;

			// Сохранить запись о placeholder-е.
			$compiled[] = array($key, $type, $start, $p - $start);
		}
		return array($compiled, $tmpl, $has_named);
	}


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ HELPERS | FORMS METHODS BELOW ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

	/**
	 * @return PDO
	 */
	private function _inst() {
		return isset($this->inst[$this->label]) ? $this->inst[$this->label] : null;
	}

	/**
	 * @param PDO
	 */
	private function _setInst(PDO $inst) {
		$this->inst[$this->label] = $inst;
	}

	/**
	 * @return PDOStatement
	 */
	private function stmt() {
		return $this->stmt ?: $this->stmt = new PDOStatement();
	}

	/**
	 * @param PDOStatement
	 */
	private function _setStmt(PDOStatement $stmt) {
		$this->stmt = $stmt;
	}
}
