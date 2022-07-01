<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

require_once(VF_SYSTEM_DIR . '/libraries/smarty/Smarty.class.php');

class Tpl extends Core
{
	private $smarty;
	private $config	= [];
	private $template;
	private $directory; //Текущая директория шаблонов
	private $ext = '.tpl';

	public function __construct()
	{
		$this->config = [
			'template_dir'		=> VF_TPLS_DIR . '/',
			'compile_dir'		=> VF_TMP_DIR . '/smarty/templates_c/',
			'cache_dir'			=> VF_TMP_DIR . '/smarty/cache/',
			'config_dir'		=> '',
			'error_reporting'	=> E_ALL & ~E_NOTICE,
			'compile_check'		=> true,
			'caching'			=> 0,
			'cache_lifetime'	=> 600,
			'debugging'			=> false
		];
		$this->smarty = $this->_instance();
	}

	public function _instance()
	{
		$smarty = new Smarty();

		//Set directories
		$smarty->setTemplateDir($this->config['template_dir']);
		$smarty->setCompileDir($this->config['compile_dir']);
		$smarty->setCacheDir($this->config['cache_dir']);

		//Configuration
		if($this->config['debugging'])
			$smarty->setDebugging($this->config['debugging']);

		if($this->config['error_reporting'])
			$smarty->setErrorReporting($this->config['error_reporting']);

		if($this->config['compile_check'])
			$smarty->compile_check = $this->config['compile_check'];

		if($this->config['caching'])
			$smarty->caching = $this->config['caching'];

		if($this->config['cache_lifetime'])
			$smarty->cache_lifetime = $this->config['cache_lifetime'];

		return $smarty;
	}

	/**
	 * Проверяет присутствие расширения в шаблоне, и добавляет его, если нужно
	 * @param string $template
	 * @return string $template.tpl
	 */
	private function _normalizeTplName($template)
	{
		return strpos($template, $this->ext) === false ? $template.$this->ext : $template;
	}

	/**
	 * Возвращает вывод шаблона вместо его отображения на экран
	 * @param string $template
	 * @param array $vars
	 * @return template result
	 */
	public function get($template = null, $vars = [])
	{
		$template = $this->_normalizeTplName($template);
		$sm = $this->_instance();
		foreach ($vars as $key => $value)
			$sm->assign($key, $value);

		return $sm->fetch( ($this->directory ? $this->directory.'/' : '') . $template );
	}

	/**
	 * Установка файла шаблона
	 * @return display template result
	 */
	public function template($template = '')
	{
		$template = $this->_normalizeTplName($template);
		$this->template = ($this->directory ? $this->directory.'/' : ''). $template;
	}

	/**
	 * Установка текушей директории шаблона
	 * @param string $directory
	 * @return void
	 */
	public function directory($directory = '')
	{
		$this->directory = trim($directory, '/');
	}

	/**
	 * Вывод результата на экран
	 * @return display template result
	 */
	public function display($vars = [])
	{
		//Прописываем все переменные
		foreach ($vars as $k => $v)
			$this->smarty->assign($k, $v);

		return $this->template ? $this->smarty->display($this->template) : null;
	}

	/**
	 * Получение текущего значения переменной шаблона
	 * @param string $var
	 * @return mixed Value of variable
	 */
	public function getVar($var)
	{
		return $this->smarty->getTemplateVars($var);
	}

	/**
	 * Установка значения переменной шаблона
	 * @param string $var
	 * @param mixed $value
	 */
	public function setVar($var, $value)
    {
        return $this->smarty->assign($var, $value);
    }

	/**
	 * Установка основной директории шаблонов
	 * @param string $dir
	 */
	public function setTemplateDir($dir)
	{
		$this->smarty->setTemplateDir($dir);
	}

	/**
	 * Очистка КЭШа
	 */
	public function clearCache()
	{
		$this->smarty->clearAllCache();
	}
}
