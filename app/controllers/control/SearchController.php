<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

//Контроллер результатов поиска
class SearchController extends AbstractControlController
{
	protected $module = 'search';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'search';
	}

	public function index()
	{
		$query = trim($this->input()->get('q', true));
		$this->vars['query'] = $query;

		/** Поиск по пользователям */

		/** Поиск по логам */

		$this->vars['header'] = 'Результат поиска';
		$this->tpl()->template($this->module.'/index');
	}
}