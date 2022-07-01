<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Result;

abstract class AbstractControlController extends AbstractController
{
	public $menu	= [];
	public $modules	= []; //Список для формирования пунктов основного меню. Array модуль => роли, имеющие доступ в него
	public $adminNotifications = [];

	public function __construct()
	{
		parent::__construct();

		$this->modules = [
			'statistics'	=> ['root','admin','manager'],
			'projects'		=> ['root','admin','manager','support'],
			'wildberries'	=> ['root','admin','manager','support'],
			'ozon'			=> ['root','admin','manager','support'],
			'operations'	=> ['root','admin','support'],
			'constants'		=> ['root','admin','support'],
			'finance'		=> ['root','admin'],
			'tariffs'		=> ['root','admin'],
			'users'			=> ['root','admin','manager','support'],
			'partners'		=> ['root','admin','manager'],
			'blog'			=> ['root','admin','seo'],
			'courses'		=> ['root','admin'],
			'filemanager'	=> ['root','admin','seo'],
			'pages'			=> ['root','admin','seo'],
			'proxies'		=> ['root','admin'],
			'snipets'		=> ['root','admin','seo'],
			'logging'		=> ['root'],
			'notices'		=> ['root','admin'],
			'api'			=> ['root','admin'],
			'sysinfo'		=> ['root'],
		];

		$this->menu = $this->modules;

		// Собираем меню из установленных модулей
		$this->vars['_menu'] = $this->menu;

		$publicURLs = [
			ADMIN.'/login',
			ADMIN.'/logout',
		];
		$segments = $this->uri()->getSegments();
		$currentUrl = $segments[0].(!empty($segments[1]) ? '/'.$segments[1] : '');
		$isAllowedUrl = in_array($currentUrl, $publicURLs);

		if (!$this->auth()->isLogged() && !$isAllowedUrl) {
			if (count($segments) > 1)
				$this->session()->set('authBackUrl', rawurlencode($this->uri()->getRequest()));
			Func::redirect(ADMIN.'/login');
		} elseif (!$this->_checkAccess($this->auth()->config('admin_roles'), true) && !$isAllowedUrl) //only Admin or Root
			throw new Exception('You do not have access rights to this section. <a href="/'.ADMIN.'/logout">Logout</a>');

		$this->vars['module'] = '';
		$this->vars['currentMenu'] = '';

		//Состояние Меню
		$this->vars['menuCollapsed'] = $this->cookie()->get('menu') == 'collapsed';

		//Обработка BackURL
		$this->vars['backQuery'] = $this->session()->get('backQuery');

		// Уведомления. Новые элементы
		$this->vars['unreaded'] = [];
		$this->vars['unreaded']['total'] = 0;

		$this->layout()->page()->setTitle('Admin - ' . SERVER_NAME);

		//Устанавливаем директорию шаблонов
		$this->tpl()->directory(ADMIN);
	}

	public function download()
	{
		$this->dl = new Download();
		$this->dl->exec();
		exit($this->dl->getError());
	}

	/**
	 * Устанавливаем GET-параметры в сессию, чтобы возвращаться в исходное положение фильтров
	 * @return void
	 */
	protected function _setBackQuery()
	{
		$queryString = $this->input()->server('QUERY_STRING');
		if($queryString)
			$this->session()->set('backQuery', '?'.$queryString);
		else
			$this->session()->unset('backQuery');

		return;
	}

	// Проверка прав доступа в раздел
	protected function _checkAccess($roles = [], $return = false)
	{
		if (!$this->currentUser()->getRole() || !in_array($this->currentUser()->getRole(), $roles)) {
			if ($return) return false;
			else Func::redirect(ADMIN.'/forbidden');
		}
		return true;
	}

	public function forbidden()
	{
		$this->layout()->page()->setHeader('У Вас нет прав для доступа в этот раздел');
		$this->tpl()->template('index/forbidden');
	}
// -END Проверка прав доступа в раздел

	public function login()
	{
		$result = new Result();

		if ($this->input()->post('primary')) {
			$email = $this->input()->post('email', true);
			$password = $this->input()->post('password', true);
			$result = $this->auth()->login($email, $password);
		}

		// Возвращаем пользователя
		if ($this->auth()->isLogged()) {
			$authBackUrl = $this->session()->get('authBackUrl') ? rawurldecode($this->session()->get('authBackUrl')) : ADMIN;
			$this->session()->unset('authBackUrl');
			Func::redirect($authBackUrl);
		}

		$this->vars['error'] = $result->getError();
		$this->vars['post'] = $this->input()->post();

		$this->tpl()->template('_auth/login');
	}

	public function logout()
	{
		$this->auth()->logout();
		Func::redirect(ADMIN.'/login');
	}
}
