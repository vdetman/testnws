<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\DateTime;

class StatisticsController extends AbstractControlController
{
	protected $module = 'statistics';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->addJs[] = '/admin/js/modules/statistics.js'.Func::modifyTime('/admin/js/modules/statistics.js');
		$this->addCss[] = '/admin/css/modules/statistic.css'.Func::modifyTime('/admin/css/modules/statistic.css');
	}

	public function index()
	{
		$since = date('Y-m-d', time() - 30 * 86400);
		$until = date('Y-m-d');

		$this->vars['usersActivityData'] = $this->statistic()->common()->usersActivity($since, $until);

		$this->vars['financeActivityData'] = $this->statistic()->common()->financeActivity($since, $until);

		$this->vars['byModulesWbData'] = $this->statistic()->common()->byModules($since, $until, MARKETPLACE_ID_WILDBERRIES);
		$this->vars['byModulesOzonData'] = $this->statistic()->common()->byModules($since, $until, MARKETPLACE_ID_OZON);

		$this->vars['currentMenu'] = 'index';

		$this->addCss[] = '/admin/plugins/morris/morris.css'.Func::modifyTime('/admin/plugins/morris/morris.css');
		$this->addJs[] = '/admin/plugins/morris/morris.min.js';
		$this->addJs[] = '/admin/plugins/morris/raphael.min.js';

		$this->layout()->page()->setHeader('Статистика');
		$this->tpl()->template($this->module.'/index');
	}

	public function managers()
	{
		// Фильтрация для операций
		$filter = new Filter([
			'since'		=> date('Y-m-d', time() - 30 * 86400),
			'until'		=> date('Y-m-d'),
			'status'	=> 'success',
			'type_id'	=> OPERATION_TYPE_REFILL,
			'ResponsibleUserIsActive' => 'true',
		]);

		// period
		if (trim($this->input()->get('since')) != '')
			$filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until')) != '')
			$filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		/*// ResponsibleUserStatus
		if (null !== $this->input()->get('ResponsibleUserIsActive')) {
			$v = strval($this->input()->get('ResponsibleUserIsActive'));
			switch($v){
				case 'all':break;
				default: $filter->set('ResponsibleUserIsActive', $v); break;
			}
		}*/

		$this->vars['managersGraphs'] = $this->statistic()->common()->getManagersGraphs($filter);
		$this->vars['currentMenu'] = 'managers';
		$this->vars['filter'] = $filter;

		$this->addJs[] = '/admin/plugins/chartjs/chart.min.js';


		$this->layout()->page()->setHeader('Статистика отдела продаж');
		$this->tpl()->template($this->module.'/managers');
	}

	public function operations()
	{
		$allowedTypes = [OPERATION_TYPE_REFILL, OPERATION_TYPE_BONUS];

		//Фильтрация
		$filter = new Filter([
			'type_id'	=> $allowedTypes,
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'operation_id',
			'order_dir'	=> 'DESC',
		]);

		// ID
		if (intval($this->input()->get('operation_id', true)))
			$filter->set('operation_id', intval($this->input()->get('operation_id', true)));

		// Status
		if (!is_null($this->input()->get('status'))) {
			$v = strval($this->input()->get('status'));
			switch($v){
				case 'all':break;
				default: $filter->set('status', $v); break;
			}
		}

		// Type
		if (!is_null($this->input()->get('type_id'))) {
			$v = strval($this->input()->get('type_id'));
			switch($v){
				case 'all':break;
				default: $filter->set('type_id', $v); break;
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

		// Method
		if (!is_null($this->input()->get('method'))) {
			$v = strval($this->input()->get('method'));
			switch($v){
				case 'all':break;
				default: $filter->set('method', $v); break;
			}
		}

		// IsFirstRefill
		if (!is_null($this->input()->get('is_first_refill'))) {
			$v = strval($this->input()->get('is_first_refill'));
			switch($v){
				case 'all':break;
				default: $filter->set('is_first_refill', $v); break;
			}
		}

		// orderBy
		if (!is_null($orderBy = $this->input()->get('order', true))) {
			$filter->set('order', $orderBy);
			switch($orderBy){
				case 'id_asc':		$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'operation_id'); break;
				case 'id_desc':		$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'operation_id'); break;
				case 'amount_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'amount'); break;
				case 'amount_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'amount'); break;
			}
		}

		// period
		if (trim($this->input()->get('since')) != '')
			$filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until')) != '')
			$filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		// Amount
//		if (intval($this->input()->get('AmountMin'))) $filter->set('AmountMin', intval($this->input()->get('AmountMin')));
//		if (intval($this->input()->get('AmountMax'))) $filter->set('AmountMax', intval($this->input()->get('AmountMax')));

		// Search
		if (trim($this->input()->get('search')))
			$filter->set('search', trim($this->input()->get('search')));

		$filter->formLimits();
		$operations = $this->finance()->operations()->gets($filter);
		//echo '<pre>',print_r($operations, 1),'</pre>'; die();
		$this->vars['operations'] = $operations;

		$filter->setTotal($this->finance()->operations()->getTotal());

		$this->vars['managers'] = $this->amo()->getManagers();

		$this->vars['filter'] = $filter;

		// Общая сумма
		$totalAmount = 0;
		if ($filter->has('type_id') && 'all' != $filter->get('type_id'))
			$totalAmount = number_format($this->finance()->operations()->getTotalAmount($filter), 2, '.', ' ');
		$this->vars['totalAmount'] = $totalAmount;

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

		$this->addCss[] = '/admin/css/modules/operations.css'.Func::modifyTime('/admin/css/modules/operations.css');

		$this->vars['currentMenu'] = 'operations';
		$this->layout()->page()->setHeader('Список операций');
		$this->tpl()->template($this->module.'/operations');
	}

	// Создание
	public function createOperation()
	{
		$this->_createOperation();

		$this->addJs[] = '/admin/js/jquery.autocomplete.min.js';
		$this->addJs[] = '/admin/plugins/select2/select2.min.js';
		$this->addCss[] = '/admin/plugins/select2/select2.css';
		$this->addJs[] = '/admin/js/modules/operations.js'.Func::modifyTime('/admin/js/modules/operations.js');

		$this->vars['currentMenu'] = 'operations';
		$this->layout()->page()->setHeader('Создание бонусной операции');
		$this->tpl()->template($this->module.'/createOperation');
	}

	private function _createOperation()
	{
		$errors = [];

		if($this->input()->post()){

			$this->vars['post'] = $post = $this->input()->post('field');

			if(!$post['TypeId'] || !in_array($post['TypeId'], [OPERATION_TYPE_BONUS]))
				$errors[] = 'Не указан Тип операции';

			$post['Amount'] = round($post['Amount'], 2);
			if (0 >= $post['Amount'])
				$errors[] = 'Не указана сумма';

			$user = $this->users()->get(intval($post['UserId']));
			if (!$user)
				$errors[] = 'Пользователь не найден';

			// Если насобирали ошибки, то выход..
			if ($errors) {
				$this->_notify(implode('<br/>', $errors), false);
				return;
			}

			$post['description'] = trim($post['description']);

			// добавляем запись
			$operation = $this->finance()->operations()->_new()
				->setTypeId($post['TypeId'])
				->setAmount($post['Amount'])
				->setMethod('account')
				->setUserId($user->getId())
				->setStatus('success')
				->setCreated(new DateTime())
				->setCompleted(new DateTime());

			if ($post['description'])
				$operation->setDescription($post['description']);

			if (!$this->finance()->operations()->create($operation)) {
				$this->_notify('Ошибка создания', false);
			} else {

				// Триггер события для MQ
				if (OPERATION_TYPE_BONUS == $operation->getTypeId()) {
					$o = $this->finance()->operations()->get($operation->getId());
					$this->events()->trigger(EVENTS_SCOPE_FINANCE, EVENTS_EVENT_SUCCESS, $o, ['reason' => 'manual', '_method' => '_create']);
				}

				$this->_notify('Операция #' . $operation->getId() . ' успешно создана');
				Func::redirect(ADMIN . '/' . $this->module . '/operations');
			}
		}
	}
}