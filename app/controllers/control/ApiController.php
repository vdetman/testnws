<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;

class ApiController extends AbstractControlController
{
	protected $module = 'api';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->addJs[] = '/admin/js/modules/api.js'.Func::modifyTime('/admin/js/modules/api.js');
	}

	public function index()
	{

	}
}