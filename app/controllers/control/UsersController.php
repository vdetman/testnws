<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Users\Entity\User;

class UsersController extends AbstractControlController
{
	protected $module = 'users';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'user_id',
			'order_dir'	=> 'DESC',
		]);

		// orderBy
		if (!is_null($orderBy = $this->input()->get('order', true))) {
			$filter->set('order', $orderBy);
			switch($orderBy){
				case 'id_asc':		$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'user_id'); break;
				case 'id_desc':		$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'user_id'); break;
			}
		}

		// Status
		if (!is_null($this->input()->get('status'))) {
			switch($this->input()->get('status')){
				case 'all':break;
				default: $filter->set('status', $this->input()->get('status')); break;
			}
		}

		// RoleId
		if (!is_null($this->input()->get('role_id'))) {
			switch($this->input()->get('role_id')){
				case 'all':break;
				default: $filter->set('role_id', $this->input()->get('role_id')); break;
			}
		}

		// CreatedSince
		if (trim($this->input()->get('since'))) $filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until'))) $filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		// Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		//echo '<pre>',print_r($this->users()->getRoles(), 1),'</pre>'; die();

		$roles = [];
		foreach($this->users()->getRoles() as $role => $roleId) {
			$roles[$role] = $roleId;
		}
		$this->vars['roles'] = $roles;

		$filter->formLimits();
		$users = $this->users()->gets($filter);
		$this->vars['users'] = $users;

		$filter->set('roles', $roles);
		$filter->setTotal( $this->users()->getTotal());

		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('per_page'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		//Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addJs[] = '/admin/js/modules/users.js'.Func::modifyTime('/admin/js/modules/users.js');
		$this->addCss[] = '/admin/css/modules/users.css'.Func::modifyTime('/admin/css/modules/users.css');

		$this->layout()->page()->setHeader('Пользователи');
		$this->tpl()->template($this->module.'/index');
	}

	public function edit($userId = false)
	{
		$user = $this->users()->get($userId);
		if(!$user) Func::redirect(ADMIN.'/'.$this->module);

		// Saving
		if ($this->_save($user))
			$user = $this->users()->get($userId);

		$this->vars['user'] = $user;

		$this->vars['currentMenu'] = 'users';
		$this->vars['header'] = 'Редактирование Пользователя';
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param User
	 * @return boolean
	 */
	public function _save(User $item)
	{
		if ($this->input()->post('save')) {

			$errors = [];

			//POST
			$fields = $this->input()->post('profile');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['email','phone','last_name','first_name','second_name']))
					unset($fields[$field]);

			if (!$fields['email']) $errors[] = 'Не указан Email';

			//Если Email меняется, то проверим на уникальность
			if ($fields['email'] && $fields['email'] != $item->getEmail() && $this->users()->getByEmail($fields['email']))
				$errors[] = 'Указанный E-mail уже используется<br />';

			if (!$fields['first_name']) $errors[] = 'Не указано Имя';

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) return $this->_notify(implode('<br/>', $errors), false);

			//обновляем запись
			$c = $this->users()->_new()
				->setEmail($fields['email'])
				->setPhone($fields['phone'])
				->setLastName($fields['last_name'])
				->setFirstName($fields['first_name'])
				->setSecondName($fields['second_name']);
			if ($this->users()->update($item->getId(), $c->toArray()))
				return $this->_notify('Изменения успешно сохранены');
			else
				return $this->_notify('Ошибка обновления', false);
		}
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$value = (int) $this->input()->post('value', true);

		$itemInfo = UserModel::get($id);
		if(!$itemInfo){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Пользователь не найден';
			die (json_encode($this->ajaxResponse));
		}

		if($itemInfo['user_id'] == $this->currentUser()->getId()){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Нельзя менять данный параметр самому себе';
			die (json_encode($this->ajaxResponse));
		}

		if($this->currentUser()->getRole() != 'root' && in_array($itemInfo['role'], ['root','admin'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'У Вас нет прав для этой операции';
			die (json_encode($this->ajaxResponse));
		}

		if(!in_array($field, ['status','IsPartner'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимое значение $field: '.$field;
			die (json_encode($this->ajaxResponse));
		}

		if(!in_array($value, [0, 1])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение данных: '.$field.' - '.$value;
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->users()->_new();
		switch ($field) {
			case 'status':
				$u->setStatus($value == 1 ? 'active' : 'blocked');
				break;
			case 'IsPartner':
				$u->setIsPartner(boolval($value));
				break;
		}

		//Обновление
		if(!$this->users()->update($id, $u->toArray())){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

// summary

	public function summary()
	{
		//Фильтрация
		$filter = new Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'user_id',
			'order_dir'	=> 'DESC',
		]);

		// orderBy
		if (!is_null($orderBy = $this->input()->get('order', true))) {
			$filter->set('order', $orderBy);
			switch($orderBy){
				case 'id_asc':				$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'user_id'); break;
				case 'id_desc':				$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'user_id'); break;
				case 'balance_asc':			$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'balance'); break;
				case 'balance_desc':		$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'balance'); break;
				case 'daily_amount_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'daily_amount'); break;
				case 'daily_amount_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'daily_amount'); break;
				case 'enough_days_asc':		$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'enough_days'); break;
				case 'enough_days_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'enough_days'); break;
				case 'refills_asc':			$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'refills'); break;
				case 'refills_desc':		$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'refills'); break;
				case 'bonuses_asc':			$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'bonuses'); break;
				case 'bonuses_desc':		$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'bonuses'); break;
				case 'last_refill_asc':		$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'last_refill_at'); break;
				case 'last_refill_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'last_refill_at'); break;
				case 'last_costed_asc':		$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'last_costed_at'); break;
				case 'last_costed_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'last_costed_at'); break;
			}
		}

		// Status
		if (!is_null($this->input()->get('status_id'))) {
			$v = intval($this->input()->get('status_id'));
			switch($v){
				case 'all':break;
				default: $filter->set('status_id', $v); break;
			}
		}

		// ManagerId
		if (!is_null($this->input()->get('manager_id'))) {
			$v = intval($this->input()->get('manager_id'));
			switch($v){
				case 'all':break;
				default: $filter->set('manager_id', $v); break;
			}
		}

		// Modules
		if ($modules = $this->input()->get('modules'))
			$filter->set('modules', $modules);

		// CreatedSince
		if (trim($this->input()->get('since'))) $filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until'))) $filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		if (strlen($this->input()->get('balance_min'))) $filter->set('balance_min', intval($this->input()->get('balance_min')));
		if (strlen($this->input()->get('balance_max'))) $filter->set('balance_max', intval($this->input()->get('balance_max')));
		if (strlen($this->input()->get('refills_min'))) $filter->set('refills_min', intval($this->input()->get('refills_min')));
		if (strlen($this->input()->get('refills_max'))) $filter->set('refills_max', intval($this->input()->get('refills_max')));
		if (strlen($this->input()->get('enough_days_min'))) $filter->set('enough_days_min', intval($this->input()->get('enough_days_min')));
		if (strlen($this->input()->get('enough_days_max'))) $filter->set('enough_days_max', intval($this->input()->get('enough_days_max')));

		// Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$statuses = [];
		foreach($this->users()->summary()->getStatuses() as $st)
			$statuses[] = $st;
		$filter->set('statuses', $statuses);

		$this->vars['managers'] = $this->amo()->getManagers();
		//echo '<pre>',print_r($this->vars['managers'], 1),'</pre>'; die();

		$this->vars['modules'] = $this->users()->summary()->getModules();
		$this->vars['marketplaces'] = $this->users()->summary()->getMarketplaces();

		$filter->formLimits();
		$users = $this->users()->summary()->gets($filter);
		$this->vars['users'] = $users;

		//echo '<pre>',print_r($users, 1),'</pre>'; die();

		$filter->setTotal($this->users()->summary()->getTotal());

		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('per_page'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		//Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addJs[] = '/admin/js/modules/users.js'.Func::modifyTime('/admin/js/modules/users.js');
		$this->addCss[] = '/admin/css/modules/users.css'.Func::modifyTime('/admin/css/modules/users.css');

		$this->layout()->page()->setHeader('Общая сводка по пользователям');
		$this->tpl()->template($this->module.'/summary/index');
	}

	public function refreshSummary()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$summary = $this->users()->summary()->get(intval($this->input()->post('uid', true)));
		if (!$summary) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Object not found';
			die (json_encode($this->ajaxResponse));
		}

		$result = $this->users()->summary()->refreshSummary($summary->getUserId());
		if (!$result->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $result->getError();
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Сводка обновлена успешно';
		$this->ajaxResponse['summary'] = $this->tpl()->get($this->module.'/summary/_item', [
			's'				=> $this->users()->summary()->get($summary->getUserId()),
			'managers'		=> $this->amo()->getManagers(),
			'modules'		=> $this->users()->summary()->getModules(),
			'marketplaces'	=> $this->users()->summary()->getMarketplaces(),
			'this'			=> $this
		]);
		die(json_encode($this->ajaxResponse));
	}

	public function addNewStatusModal()
	{
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("{$this->module}/summary/_addNewStatusModal", ['styles' => $this->users()->summary()->getStatusStyles()]);
		die(json_encode($this->ajaxResponse));
	}

	public function addNewStatusConfirm()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$params = [];
		parse_str($this->input()->post('form'), $params);
		if (!$params) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Something has gone wrong';
			die (json_encode($this->ajaxResponse));
		}

		$name = isset($params['statusName']) ? trim(mb_strtolower($params['statusName'])) : '';
		$style = isset($params['statusStyle']) ? trim(mb_strtolower($params['statusStyle'])) : '';

		if (!in_array($style, $this->users()->summary()->getStatusStyles())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не указан стиль оформления';
			die (json_encode($this->ajaxResponse));
		}

		if (!$name) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не указано название';
			die (json_encode($this->ajaxResponse));
		}

		foreach ($this->users()->summary()->getStatuses() as $es) {
			if ($es->getName() == $name) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Указанный статус уже существует';
				die (json_encode($this->ajaxResponse));
			}
		}

		$ns = $this->users()->summary()->_newStatus()->setName($name)->setStyle($style);
		if (!$this->users()->summary()->createStatus($ns)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die (json_encode($this->ajaxResponse));
		}

		die(json_encode($this->ajaxResponse));
	}

	public function editSummaryModal()
	{
		if (!$this->input()->isAjax()) die();
		$summary = $this->users()->summary()->get(intval($this->input()->post('uid', true)));
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("{$this->module}/summary/_editSummaryModal", [
			'summary'	=> $summary,
			'statuses'	=> $this->users()->summary()->getStatuses(),
			'managers'	=> $this->amo()->getManagers()
		]);
		die(json_encode($this->ajaxResponse));
	}

	public function editSummarySave()
	{
		if (!$this->input()->isAjax()) die();

		$params = [];
		parse_str($this->input()->post('form'), $params);
		if (!$params) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Something has gone wrong';
			die (json_encode($this->ajaxResponse));
		}

		// Oblect
		$summary = $this->users()->summary()->get(intval($params['user_id']));
		if (!$summary) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Object not found';
			die(json_encode($this->ajaxResponse));
		}

		// Statuses
		$statuses = $this->users()->summary()->getStatuses();
		$statusId = isset($statuses[intval($params['status_id'])]) ? intval($params['status_id']) : false;

		// Managers
		$managers = $this->amo()->getManagers();
		$managerId = isset($managers[intval($params['manager_id'])]) ? intval($params['manager_id']) : false;

		// Comment
		$comment = trim($params['comment']);

		$n = $this->users()->summary()->_new()
			->setStatusId($statusId)
			->setManagerId($managerId)
			->setComment($comment)
			->setUpdatedAt(new Entity\DateTime());
		if (!$this->users()->summary()->update($summary->getUserId(), $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Запись изменена успешно';
		$this->ajaxResponse['summary'] = $this->tpl()->get($this->module.'/summary/_item', [
			's'				=> $this->users()->summary()->get($summary->getUserId()),
			'managers'		=> $this->amo()->getManagers(),
			'modules'		=> $this->users()->summary()->getModules(),
			'marketplaces'	=> $this->users()->summary()->getMarketplaces(),
			'this'			=> $this
		]);

		die(json_encode($this->ajaxResponse));
	}

	public function statusesModal()
	{
		if (!$this->input()->isAjax()) die();

		$statuses = $this->users()->summary()->getStatuses();
		$statusesUsage = $this->users()->summary()->getStatusesUsage();

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("{$this->module}/summary/_statusesModal", [
			'statuses'		=> $statuses,
			'statusesUsage'	=> $statusesUsage,
			'this'			=> $this
		]);
		die(json_encode($this->ajaxResponse));
	}

	public function deleteStatus()
	{
		if (!$this->input()->isAjax()) die();

		$statusesUsage = $this->users()->summary()->getStatusesUsage();

		$sid = intval($this->input()->post('sid', true));
		if (isset($statusesUsage[$sid])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить этот статус';
			die(json_encode($this->ajaxResponse));
		}

		if (!$this->users()->summary()->deleteStatus($sid)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не удалось удалить статус';
			die(json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Статус успешно удалён';
		die(json_encode($this->ajaxResponse));
	}

	public function formOffer()
	{
		if (!$this->input()->isAjax()) die();
		$summary	= $this->users()->summary()->get(intval($this->input()->post('uid', true)));
		$managers	= $this->amo()->getManagers();

		$manager = $summary->getManagerId() && isset($managers[$summary->getManagerId()]) ? $managers[$summary->getManagerId()]->getName() : 'Stat4Market';

		// Суточное списание
		$dCost = intval($summary->getDailyAmount());

		$_1m_cost = $dCost * 30;
		$_3m_cost = $dCost * 90;
		$_6m_cost = $dCost * 180;
		$_1y_cost = $dCost * 365;

		//echo '<pre>',print_r([$_1m_cost,$_3m_cost,$_6m_cost,$_1y_cost], 1),'</pre>';

		$_1m_cost = $this->_roundThousand($_1m_cost);
		$_3m_cost = $this->_roundThousand($_3m_cost);
		$_6m_cost = $this->_roundThousand($_6m_cost);
		$_1y_cost = $this->_roundThousand($_1y_cost);

		$_1m_bonus_perc = $this->_bonusPercent($_1m_cost);
		$_3m_bonus_perc = $this->_bonusPercent($_3m_cost);
		$_6m_bonus_perc = $this->_bonusPercent($_6m_cost);
		$_1y_bonus_perc = $this->_bonusPercent($_1y_cost);

		$_1m_bonus = $_1m_cost * $_1m_bonus_perc / 100;
		$_3m_bonus = $_3m_cost * $_3m_bonus_perc / 100;
		$_6m_bonus = $_6m_cost * $_6m_bonus_perc / 100;
		$_1y_bonus = $_1y_cost * $_1y_bonus_perc / 100;

		//echo '<pre>',print_r([$_1m_cost,$_3m_cost,$_6m_cost,$_1y_cost], 1),'</pre>'; die();

		$lines = [];

		// 1 month
		$line = '1 месяц - ' . number_format($_1m_cost, 0, '.', ' ') . 'р.';
		if ($_1m_bonus)
			$line .= " + бонус {$_1m_bonus_perc}% - " . number_format($_1m_bonus, 0, '.', ' ') . 'р.';
		$lines[] = $line;

		// 3 month
		$line = '3 месяца - ' . number_format($_3m_cost, 0, '.', ' ') . 'р.';
		if ($_3m_bonus)
			$line .= " + бонус {$_3m_bonus_perc}% - " . number_format($_3m_bonus, 0, '.', ' ') . 'р.';
		$lines[] = $line;

		// 6 month
		$line = '6 месяцев - ' . number_format($_6m_cost, 0, '.', ' ') . 'р.';
		if ($_6m_bonus)
			$line .= " +{$_6m_bonus_perc}%, " . number_format($_6m_bonus, 0, '.', ' ') . 'р.';
		$lines[] = $line;

		// year
		$line = '12 месяцев - ' . number_format($_1y_cost, 0, '.', ' ') . 'р.';
		if ($_1y_bonus)
			$line .= " +{$_1y_bonus_perc}%, " . number_format($_1y_bonus, 0, '.', ' ') . 'р.';
		$lines[] = $line;

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("{$this->module}/summary/_offerModal", [
			'summary'	=> $summary,
			'manager'	=> $manager,
			'lines'		=> $lines
		]);
		die(json_encode($this->ajaxResponse));
	}

	private function _bonusPercent($amount = 0)
	{
		$bonuses = [
			['A' => 10000,	'P' => 10],
			['A' => 25000,	'P' => 20],
			['A' => 50000,	'P' => 30],
			['A' => 100000,	'P' => 40],
			['A' => 150000,	'P' => 50]
		];
		$bonusPercent = 0;
		foreach ($bonuses as $b)
			if ($amount >= $b['A']) $bonusPercent = $b['P'];
		return $bonusPercent;
	}

	private function _roundThousand($amount = 0)
	{
		return floor($amount / 1000) * 1000;
	}
}