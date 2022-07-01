<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

//Системный Модуль
class SysinfoController extends AbstractControlController
{
	protected $module = 'sysinfo';

	public function __construct()
	{
		parent::__construct();

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->vars['module'] = $this->module;
	}

	public function phpinfo()
	{
		if($this->input()->get('view') !== null){
			phpinfo();
			return;
		}
		$this->vars['currentMenu'] = 'phpinfo';
		$this->layout()->page()->setHeader('PHP Info');
		$this->tpl()->template($this->module.'/phpinfo');
	}

	public function constants()
	{
		$type = $this->input()->get('type', true);
		if (null !== $this->input()->get('view')) {
			$definedConstants = get_defined_constants(boolval($type));
			echo '<pre>'.print_r($type ? $definedConstants[$type] : $definedConstants, 1).'</pre>';
			return;
		}

		$this->vars['type'] = $type;
		$this->vars['currentMenu'] = 'constants';
		$this->layout()->page()->setHeader('Объявленные в Системе константы');
		$this->tpl()->template($this->module.'/constants');
	}

	public function cacheinfo()
	{
		$this->vars['config'] = $this->cache()->config();
		$this->vars['allKeys'] = $this->cache()->getAllKeys() ?: [];
		$this->vars['allTags'] = $this->cache()->getAllTags() ?: [];

		$this->vars['currentMenu'] = 'cacheinfo';
		$this->layout()->page()->setHeader('Использование КЭШ');
		$this->tpl()->template($this->module.'/cacheinfo');
	}
}