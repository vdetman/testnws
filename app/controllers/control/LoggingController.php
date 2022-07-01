<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

//Модуль управления Логами
class LoggingController extends AbstractControlController
{
	protected $module = 'logging';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'logging';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Entity\Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'perPage'	=> ADMIN_PERPAGE,
			'orderBy'	=> $this->input()->get('orderBy') ?: 'id',
			'orderDir'	=> $this->input()->get('orderDir') ?: 'Desc',
		]);

		//agent
		if($this->input()->get('agent') !== null){
			switch($this->input()->get('agent')){
				case 'all':break;
				case 'user':	$filter->set('Type', 'human'); $filter->set('isUser', true); break;
				case 'human':   $filter->set('Type', 'human'); $filter->set('isUser', false); break;
				case 'robot':   $filter->set('Type', 'robot'); break;
				case 'norobot': $filter->set('Type', 'human'); break;
			}
		}

		// period
		if(trim($this->input()->get('periodMin')) != '')
			$filter->set('periodMin', date('Y-m-d', strtotime(trim($this->input()->get('periodMin')))));
		if(trim($this->input()->get('periodMax')) != '')
			$filter->set('periodMax', date('Y-m-d', strtotime(trim($this->input()->get('periodMax')))));

		// Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$this->vars['logs'] = $this->logs()->gets($filter);

		$filter->setTotal($this->logs()->getTotal());

		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('perPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		$this->addCss[] = '/admin/css/modules/logging.css'.Func::modifyTime('/admin/css/modules/logging.css');
		$this->addJs[] = '/admin/js/modules/logging.js'.Func::modifyTime('/admin/js/modules/logging.js');

		$this->vars['currentMenu'] = 'log_index';
		$this->vars['header'] = 'Логи запросов страниц сайта';
		$this->tpl()->template($this->module.'/index');
	}

	public function sms()
	{
		//Фильтрация
		$filter = new Entity\Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'perPage'	=> ADMIN_PERPAGE,
			'orderBy'	=> $this->input()->get('orderBy') ?: 'id',
			'orderDir'	=> $this->input()->get('orderDir') ?: 'desc',
		]);

		//status
		if($this->input()->get('status') !== null){
			switch($this->input()->get('status')){
				case 'all':break;
				case 'error':	$filter->set('status', 'error'); break;
				case 'success': $filter->set('status', 'success'); break;
			}
		}

		//period
		if(trim($this->input()->get('periodMin')) != '')
			$filter->set('periodMin', date('Y-m-d', strtotime(trim($this->input()->get('periodMin')))));
		if(trim($this->input()->get('periodMax')) != '')
			$filter->set('periodMax', date('Y-m-d', strtotime(trim($this->input()->get('periodMax')))));

		//Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$itemList = SmsModel::getsLog($filter);
		foreach ($itemList as &$item) {
			if($item['response']){
				$resp = '';
				$decoded = json_decode($item['response'], 1);
				if(!is_null($decoded)){
					foreach($decoded as $k=>$v)
						$resp .= $k.': '.$v.'<br/>';
				}else{
					$resp = nl2br($item['response']);
				}
				$item['response'] = $resp;
			}
			if($item['result']){
				$resp = '';
				foreach(json_decode($item['result'], 1) as $k=>$v)
					$resp .= $k.': '.$v.'<br/>';
				$item['result'] = $resp;
			}
		}
		$this->vars['itemList'] = $itemList;

		$filter->setTotal(SmsModel::$totalRows);

		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('perPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		$this->addCss[] = '/admin/css/modules/logging.css'.Func::modifyTime('/admin/css/modules/logging.css');
		$this->addJs[] = '/admin/js/modules/logging.js'.Func::modifyTime('/admin/js/modules/logging.js');

		$this->vars['currentMenu'] = 'log_sms';
		$this->vars['header'] = 'Логи SMS';
		$this->tpl()->template($this->module.'/sms');
	}

	public function email()
	{
		//Фильтрация
		$filter = new Entity\Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'perPage'	=> ADMIN_PERPAGE,
			'orderBy'	=> $this->input()->get('orderBy') ?: 'id',
			'orderDir'	=> $this->input()->get('orderDir') ?: 'desc',
		]);

		//status
		if($this->input()->get('status') !== null){
			switch($this->input()->get('status')){
				case 'all':break;
				case 'error':	$filter->set('status', 'error'); break;
				case 'success': $filter->set('status', 'success'); break;
			}
		}

		//period
		if(trim($this->input()->get('periodMin')) != '')
			$filter->set('periodMin', date('Y-m-d', strtotime(trim($this->input()->get('periodMin')))));
		if(trim($this->input()->get('periodMax')) != '')
			$filter->set('periodMax', date('Y-m-d', strtotime(trim($this->input()->get('periodMax')))));

		//Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$this->vars['itemList'] = EmailModel::getsLog($filter);

		$filter->setTotal(EmailModel::$totalRows);

		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('perPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		$this->addCss[] = '/admin/css/modules/logging.css'.Func::modifyTime('/admin/css/modules/logging.css');
		$this->addJs[] = '/admin/js/modules/logging.js'.Func::modifyTime('/admin/js/modules/logging.js');

		$this->vars['currentMenu'] = 'log_email';
		$this->vars['header'] = 'Логи E-mail';
		$this->tpl()->template($this->module.'/email');
	}

// НАСТРОЙКИ ЛОГИРОВАНИЯ
	public function settings()
	{
		if($this->input()->post('submit')){
			//Собираем массив для обновления
			$fields = $this->input()->post('field', true);

			//обновляем запись в БД
			foreach($fields as $property => $value)
				$this->logs()->updateSettings($property, ['Value' => $value]);

			$this->session()->setFlash('result', [
				'status' => true,
				'descr' => 'Настройки успешно сохранены',
			]);
		}

		$settingsList = $this->logs()->getSettings();
		$settingsList['exceptions'] = implode("\n", $settingsList['exceptions']);
		$this->vars['settingsList'] = $settingsList;

		//Flash
		$this->vars['result'] = $this->session()->flash('result');

		$this->addCss[] = '/admin/css/modules/logging.css'.Func::modifyTime('/admin/css/modules/logging.css');
		$this->addJs[] = '/admin/js/modules/logging.js'.Func::modifyTime('/admin/js/modules/logging.js');

		$this->vars['currentMenu'] = 'log_settings';
		$this->vars['header'] = 'Настройки логирования';
		$this->tpl()->template($this->module.'/settings');
	}

	/**
	 * Очистка логов
	 */
	public function clear()
	{
		//Проверяем корректность запроса
		if(!$this->input()->isAjax()) die();

		switch($this->input()->post('type', true)){
			case 'general':
				$this->ajaxResponse['descr'] = 'Таблица логов посещений очищена!';
				$this->logs()->clear();
			break;
			case 'sms':
				$this->ajaxResponse['descr'] = 'Таблица логов SMS очищена!';
				SmsModel::clearLog();
			break;
			case 'email':
				$this->ajaxResponse['descr'] = 'Таблица логов E-mail очищена!';
				EmailModel::clearLog();
			break;
			default:
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Недопустимое значение';
			break;
		}
		die (json_encode($this->ajaxResponse));
	}
// -END НАСТРОЙКИ ЛОГИРОВАНИЯ
}