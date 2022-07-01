<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

/**
 * @method \Db db()
 * @method \Tpl tpl()
 * @method \Uri uri()
 * @method \Load load()
 * @method \News news()
 * @method \Agent agent()
 * @method \Cache cache()
 * @method \Input input()
 * @method \Cookie cookie()
 * @method \Layout layout()
 * @method \Router router()
 * @method \Encrypt encrypt()
 * @method \Session session()
 */
class Core
{
	private $initialized;
	private static $classes = [];

	public function __construct()
	{
		$this->_init();
	}

	/**
	 * Первичная инициализация
	 */
	private function _init()
	{
		if (is_null($this->initialized)) {
			@set_exception_handler([$this, 'exception_handler']);
			date_default_timezone_set(DEFAULT_TIMEZONE); // timezone
			@ini_set('display_errors', DISPLAY_ERRORS); // display_errors
			error_reporting(!empty(ERROR_REPORTING) ? ERROR_REPORTING : null); // error_reporting
			$this->initialized = true;
		}
	}

	/**
	 * Старт приложения
	 * @param string $controller
	 * @param string $method
	 * @param array $arguments
	 */
	public function run($controller = null, $method = null, $arguments = [])
	{
		if (empty($controller) || empty($method)) {
			// Routing
			$router = new Router();
			$router->parseRoutes();
			$controller = $router->getClass();		//запрошенный контроллер
			$method = $router->getMethod();			//запрошенный метод
			$arguments = $router->getArguments();	//аргументы
		}

		// Вызываем запрошенный контроллер и его метод
		call_user_func_array([$this->$controller(), $method], $arguments);
	}

	/**
	 * Custom Exception Handler
	 * @param Error
	 */
	public static function exception_handler($exception)
	{
		setStatusHeader(500);
		$traces = [];
		$c = 0;
		foreach ($exception->getTrace() as $trace) {
			if ($trace['function'] == __FUNCTION__) continue;
			$args = [];
			if (!empty($trace['args'])) {
				foreach ($trace['args'] as $arg) {
					if (is_string($arg)) {$args[] = '"' . $arg . '"';}
					elseif (is_array($arg)) {$args[] = 'array(' . count($arg) . ')';}
					elseif (is_object($arg)) {$args[] = get_class($arg) . ' object';}
				}
			}
			$traces[] = [
				'count'		=> $c++,
				'file'		=> !empty($trace['file']) ? str_replace(VF_ROOT_DIR, '', $trace['file']) . ':' . $trace['line'] : '',
				'function'	=> !empty($trace['class']) ? $trace['class'] . $trace['type'] . $trace['function'] . '('.implode(', ', $args).')' : '',
			];
		}
		echo '<pre>',print_r([
			'heading' => 'An exception was caught',
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'traces' => $traces
		], 1),'</pre>'; die();
	}

	/**
	 * @param string $class
	 * @param array $args
	 * @return mixed
	 */
	public function __call($class, array $args)
	{
		$class = ucfirst($class);

		// Если такой объект уже существует, возвращаем его
		if(isset(self::$classes[$class]))
			return self::$classes[$class];


		if (class_exists($class)) {
			self::$classes[$class] = new $class($args); // Сохраняем для будущих обращений к нему
			return self::$classes[$class]; // Возвращаем созданный объект
		}

		throw new Exception (sprintf('Call to undefined method %s::%s', get_called_class(), $class));
	}
}
