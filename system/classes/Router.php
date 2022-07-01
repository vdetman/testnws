<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Router extends Core
{
	const CONTROLLER_DEFAULT = 'index';
	const CONTROLLER_404 = 'NotFound';
	const METHOD_DEFAULT = 'index';

	protected $segments		= []; // URI segments
	protected $routes		= [
		'file/([a-zA-Z0-9]+)/?'		=> "download/file/$1",
		'report/([a-zA-Z0-9]+)/?'	=> "download/report/$1"
	];
	protected $class		= '';
	protected $method		= '';
	protected $arguments	= [];

	/**
	 *  Parse Routes
	 *  @return	void
	 */
	public function parseRoutes()
	{
		// Разбиваем URI на сегменты
		$this->segments = $this->uri()->getSegments();
		
		// URI без конечного и начального /
		$uri = implode('/', $this->segments);

		//Если есть полное совпадение с ключем ROUTES
		if(isset($this->routes[$uri])) {
			$this->segments = array_map('trim', explode('/', $this->routes[$uri]));
			return $this->_set_routing();
		}

		//Ищем совпадения с ROUTES, согласно RegExp
		foreach ($this->routes as $key => $val){
			//Convert wild-cards to RegExp
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			if (preg_match('#^'.$key.'$#', $uri)){
				// Do we have a back-reference?
				if (strpos($val, '$') !== false && strpos($key, '(') !== false)
					$val = preg_replace('#^'.$key.'$#', $val, $uri);

				$this->segments = array_map('trim', explode('/', $val));
				return $this->_set_routing();
			}
		}
		return $this->_set_routing();
	}

	/**
	 *  Set the route mapping
	 *  @return void
	 */
	private function _set_routing()
	{
		//Определим класс из первого сегмента
		$this->segments[0] = !empty($this->segments[0]) ? $this->segments[0] : self::CONTROLLER_DEFAULT;

		//Определим метод из первого сегмента
		$this->segments[1] = !empty($this->segments[1]) ? $this->segments[1] : self::METHOD_DEFAULT;

		//Ищем нужный контроллер
		$this->_find_controller();

		//Если в сегментах переданы аргументы, то установим их
		if(2 < count($this->segments))
			$this->setArguments(array_slice($this->segments, 2));

		parse_str($this->uri()->getQuery(),$_GET);

		return;
	}

	/**
	 * Ищем контроллер
	 */
	private function _find_controller()
	{
		//Ищем в app/controllers/$class.php
		$directory = VF_APP_DIR . '/controllers';
		$class = ucfirst($this->segments[0]);
		$method = $this->segments[1];
		if($this->_verify_controller($directory, $class, $method)) return true;

		//Ищем во вложенном одноименном каталоге
		$directory = VF_APP_DIR . '/controllers/' . strtolower($this->segments[0]);
		$class = ucfirst($this->segments[0]);
		$method = $this->segments[1];
		if($this->_verify_controller($directory, $class, $method)) return true;

		// Ищем запрошенный контроллер в загруженных модулях
		$class = ucfirst($class).'Controller';
		if(method_exists($class, $method)){
			$this->setClass($class);
			$this->setMethod($method);
			return true;
		}

		//Если не найден файл с именем {$class}.php, то пытаемся найти файл по второму сегменту
		//$directory = VF_APP_DIR . '/controllers/' . strtolower($this->segments[0]);
		$class = ucfirst($this->segments[1]);
		$method = !empty($this->segments[2]) ? $this->segments[2] : self::METHOD_DEFAULT;
		if($this->_verify_controller($directory, $class, $method)){
			$this->segments = array_slice($this->segments, 1); //"Сдвигаем влево" сегменты, чтобы потом правильно выделить аргументы
			return true;
		}

		//404 controller
		$directory = VF_APP_DIR . '/controllers';
		$class = self::CONTROLLER_404;
		$method = self::METHOD_DEFAULT;
		if($this->_verify_controller($directory, $class, $method)) return true;

		return false;
	}

	/**
	 * Проверяем наличие контроллера и нужного метода в нем
	 * @param string $directory
	 * @param string $class
	 * @param string $method
	 * @return bool
	 */
	private function _verify_controller($directory, $class, $method)
	{
		$directory = rtrim($directory, '/');
		$class = ucfirst($class).'Controller';
		if(is_file($directory.'/'.$class.'.php')){
			require_once $directory.'/'.$class.'.php';
			if(method_exists($class, $method)){
				$this->setClass($class);
				$this->setMethod($method);
				return true;
			}
		}
		return false;
	}

	/**
	 *  Set the class name
	 *  @param	string
	 *  @return	void
	 */
	public function setClass($class)
	{
		$this->class = str_replace(['/', '.'], '', $class);
	}

	/**
	 *  Fetch the current class
	 *  @access	public
	 *  @return	string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 *  Set the method name
	 *  @param	string
	 *  @return	void
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 *  Fetch the current method
	 *  @access	public
	 *  @return	string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 *  Set the arguments
	 *  @param  string
	 *  @return void
	 */
	public function setArguments($args)
	{
		$this->arguments = $args;
	}

	/**
	 *  Fetch the arguments
	 *  @access	public
	 *  @return	string
	 */
	public function getArguments()
	{
		return $this->arguments;
	}
}