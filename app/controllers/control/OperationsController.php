<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\DateTime;

class OperationsController extends AbstractControlController
{
	protected $module = 'operations';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'operations';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
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
		if (intval($this->input()->get('amount_min'))) $filter->set('amount_min', intval($this->input()->get('amount_min')));
		if (intval($this->input()->get('amount_max'))) $filter->set('amount_max', intval($this->input()->get('amount_max')));

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
		$this->addJs[] = '/admin/js/modules/operations.js'.Func::modifyTime('/admin/js/modules/operations.js');

		$this->vars['currentMenu'] = 'operations';
		$this->layout()->page()->setHeader('Список операций');
		$this->tpl()->template($this->module.'/index');
	}

	public function setStatus()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$operation = $this->finance()->operations()->get(intval($this->input()->post('oid', true)));
		if(!$operation){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Операция не найдена';
			die (json_encode($this->ajaxResponse));
		}

		$status = strval($this->input()->post('status', true));
		if (!in_array($status, ['new', 'success', 'error', 'canceled'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong status';
			die (json_encode($this->ajaxResponse));
		}

		if (strval($this->input()->post('hash', true)) != md5($status . '!~!' . $operation->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong HASH';
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($operation->getTypeId(), [OPERATION_TYPE_REFILL])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимый тип операции';
			die (json_encode($this->ajaxResponse));
		}

		$o = $this->finance()->operations()->_new()->setStatus($status)->setCompleted(new DateTime());

		if (!$this->finance()->operations()->update($operation->getId(), $o->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$upOper = $this->finance()->operations()->get($operation->getId());

		// Триггер события для MQ
		if ('success' == $status)
			$this->events()->trigger(EVENTS_SCOPE_FINANCE, EVENTS_EVENT_SUCCESS, $upOper, ['reason' => 'manual', '_method' => 'setStatus']);

		$this->ajaxResponse['descr'] = 'Статус операции изменен';
		$this->ajaxResponse['operation'] = $this->tpl()->get($this->module.'/_item', ['op' => $upOper, 'this' => $this]);

		die (json_encode($this->ajaxResponse));
	}

	public function setIsFirstRefillCustom()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$operation = $this->finance()->operations()->get(intval($this->input()->post('oid', true)));
		if(!$operation){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Операция не найдена';
			die (json_encode($this->ajaxResponse));
		}

		$val = strval($this->input()->post('val', true));
		if (!in_array($val, ['1', '0'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong value';
			die (json_encode($this->ajaxResponse));
		}

		if (strval($this->input()->post('hash', true)) != md5($val . '!~!' . $operation->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong HASH';
			die (json_encode($this->ajaxResponse));
		}

		if (OPERATION_TYPE_REFILL != $operation->getTypeId() || 1 == $operation->getIsFirstRefill() || 'success' != $operation->getStatus()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимый тип операции';
			die (json_encode($this->ajaxResponse));
		}

		$o = $this->finance()->operations()->_new()->setIsFirstRefillCustom($val);

		if (!$this->finance()->operations()->update($operation->getId(), $o->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Статус первичной оплаты изменен, для перерасчета общей суммы оплат, обновите страницу';
		$this->ajaxResponse['operation'] = $this->tpl()->get($this->module.'/_item', ['op' => $this->finance()->operations()->get($operation->getId()), 'this' => $this]);

		die (json_encode($this->ajaxResponse));
	}

	// Создание
	public function create()
	{
		$this->_create();

		$this->addJs[] = '/admin/js/jquery.autocomplete.min.js';
		$this->addJs[] = '/admin/plugins/select2/select2.min.js';
		$this->addCss[] = '/admin/plugins/select2/select2.css';
		$this->addJs[] = '/admin/js/modules/operations.js'.Func::modifyTime('/admin/js/modules/operations.js');

		$this->vars['currentMenu'] = 'operations';
		$this->layout()->page()->setHeader('Создание операции');
		$this->tpl()->template($this->module.'/create');
	}

	private function _create()
	{
		$errors = [];

		if($this->input()->post()){

			$this->vars['post'] = $post = $this->input()->post('field');

			if(!$post['TypeId'] || !in_array($post['TypeId'], [OPERATION_TYPE_REFUND, OPERATION_TYPE_BONUS, OPERATION_TYPE_SERVICES, OPERATION_TYPE_WITHDRAWAL]))
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

			$post['Description'] = trim($post['Description']);

			// добавляем запись
			$operation = $this->finance()->operations()->_new()
				->setTypeId($post['TypeId'])
				->setAmount($post['Amount'])
				->setMethod('account')
				->setUserId($user->getId())
				->setStatus('success')
				->setCreated(new DateTime())
				->setCompleted(new DateTime());

			if ($post['Description'])
				$operation->setDescription($post['Description']);

			if (!$this->finance()->operations()->create($operation)) {
				$this->_notify('Ошибка создания', false);
			} else {

				// Триггер события для MQ
				if (OPERATION_TYPE_BONUS == $operation->getTypeId()) {
					$o = $this->finance()->operations()->get($operation->getId());
					$this->events()->trigger(EVENTS_SCOPE_FINANCE, EVENTS_EVENT_SUCCESS, $o, ['reason' => 'manual', '_method' => '_create']);
				}

				$this->_notify('Операция #' . $operation->getId() . ' успешно создана');
				Func::redirect(ADMIN . '/' . $this->module);
			}
		}
	}

	public function delete()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$operation = $this->finance()->operations()->get(intval($this->input()->post('id', true)));
		if (!$operation) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ($this->input()->post('hash', true) != md5('delete!~!' . $operation->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong hash';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->finance()->operations()->delete($operation->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Операция удалена';
		die (json_encode($this->ajaxResponse));
	}

	public function changeManagerModal()
    {
        //Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$o = $this->finance()->operations()->get(intval($this->input()->post('oid', true)));
        if (!$o || !in_array($o->getTypeId(), [OPERATION_TYPE_REFILL])) {
            $this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'Operation not found';
            die(json_encode($this->ajaxResponse));
        }
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("{$this->module}/_changeManagerModal", ['o' => $o, 'managers' => $this->amo()->getManagers()]);
        die(json_encode($this->ajaxResponse));
    }

	public function changeManagerSave()
    {
        //Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$o = $this->finance()->operations()->get(intval($this->input()->post('oid', true)));
        if (!$o) {
            $this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'Operation not found';
            die(json_encode($this->ajaxResponse));
        }

		$n = $this->finance()->operations()->_new()->setManagerId(intval($this->input()->post('mid', true)));
		if (!$this->finance()->operations()->update($o->getId(), $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Менеджер изменен';
		$this->ajaxResponse['operation'] = $this->tpl()->get($this->module.'/_item', [
			'op'		=> $o->setManagerId($n->getManagerId()),
			'managers'	=> $this->amo()->getManagers(),
			'this'		=> $this
		]);

        die(json_encode($this->ajaxResponse));
    }

	public function downloadDocument()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$operation = $this->finance()->operations()->get(intval($this->input()->post('oid', true)));
		if (!$operation) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Операция не найдена';
			die (json_encode($this->ajaxResponse));
		}

		$object = strval($this->input()->post('object', true));
		if (!in_array($object, ['act', 'invoice'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong object';
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($operation->getTypeId(), [OPERATION_TYPE_REFILL]) || 'cashless' != $operation->getMethod()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимый тип операции';
			die (json_encode($this->ajaxResponse));
		}

		$res = $this->finance()->operations()->dlDocument($operation, $object);
		if (!$res->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $res->getError();
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['redirect'] = $res->getObject();
		die (json_encode($this->ajaxResponse));
	}
}