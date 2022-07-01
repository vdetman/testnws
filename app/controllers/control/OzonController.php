<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use Ozon\Accounts\Entity\Account as Item;

class OzonController extends AbstractControlController{
	protected $module = 'ozon';

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
			'order_by'	=> 'account_id',
			'order_dir'	=> 'DESC',
		]);

		// orderBy
		if (!is_null($orderBy = $this->input()->get('order', true))) {
			$filter->set('order', $orderBy);
			switch($orderBy){
				case 'id_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'account_id'); break;
				case 'id_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'account_id'); break;
			}
		}

		// ProjectId
		if ($this->input()->get('project_id'))
			$filter->set('project_id', $this->input()->get('project_id'));

		// AccountId
		if ($this->input()->get('account_id'))
			$filter->set('account_id', $this->input()->get('account_id'));

		// HasApiKey
		if (!is_null($this->input()->get('has_api_key'))) {
			switch($this->input()->get('has_api_key')){
				case 'all':break;
				default: $filter->set('has_api_key', boolval($this->input()->get('has_api_key'))); break;
			}
		}

		// Blocked
//		if (!empty($this->input()->get('Blocked')))
//			$filter->set('Blocked', $this->input()->get('Blocked'));

		// ProjectStatus
		if (!is_null($this->input()->get('project_status'))) {
			switch($this->input()->get('project_status')){
				case 'all':break;
				default: $filter->set('project_status', $this->input()->get('project_status')); break;
			}
		}

		// Created
		if (trim($this->input()->get('since'))) $filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until'))) $filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		// Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$filter->formLimits();
		$this->vars['list'] = $this->ozon()->accounts()->gets($filter);
		$filter->setTotal($this->ozon()->accounts()->getTotal());

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

		$this->addJs[] = '/admin/js/modules/ozon.js'.Func::modifyTime('/admin/js/modules/ozon.js');
		$this->addCss[] = '/admin/css/modules/ozon.css'.Func::modifyTime('/admin/css/modules/ozon.css');

		$this->layout()->page()->setHeader('Аккаунты Ozon');
		$this->tpl()->template($this->module.'/index');
	}

	public function edit($id = false)
	{
		if (false == ($item = $this->ozon()->accounts()->get($id))) $this->show404();

		// Saving
		$result = $this->_save($item);
		if ($result->getError() || $result->getMessage()) $this->_notify($result->getError()?:$result->getMessage(), $result->success());
		if ($result->success()) Func::redirect(ADMIN . "/{$this->module}/edit/{$item->getId()}");

		$this->vars['item'] = $item;

		$this->vars['blocked'] = $item->getActivity()->getBlockedFor() ? explode(',', $item->getActivity()->getBlockedFor()) : [];

		$this->addJs[] = '/admin/js/modules/ozon.js'.Func::modifyTime('/admin/js/modules/ozon.js');

		$this->layout()->page()->setHeader('Ozon account #' . $item->getId());
		$this->tpl()->template($this->module.'/edit');
	}

	public function _save(Item $item)
	{
		$errors = $messages = [];
		$result = new Result();

		if($this->input()->post('save'))
		{
			//POST
			$fields = $this->input()->post('field');

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['api_key','blocked']))
					unset($fields[$field]);

			$clientId = $item->getClientId();
			$apiKey = trim($fields['api_key']);

			if ($apiKey && !preg_match($this->ozon()->accounts()->getApiKeyPattern(), $apiKey))
				$errors[] = "ApiKey имеет неверный формат";

			if ($clientId && $apiKey && $apiKey != $item->getApiKey() && $this->ozon()->accounts()->existHash($this->ozon()->accounts()->_getHash($clientId, $apiKey), $item->getId()))
				$errors[] = 'Аккаунт с таким ClientId и ApiKey уже был добавлен ранее';

			$fields['client_id'] = $clientId;
			$fields['api_key'] = $apiKey;

			// Блокировки
			$blockedFor = !empty($fields['blocked'])
				? implode(',', array_intersect($fields['blocked'], ['stocks', 'orders', 'sales', 'prices', 'products', 'all']))
				: false;

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) return $result->setError(implode('<br/>', $errors));

			$messages[] = 'Изменения успешно сохранены';

			// Updating
			$n = $this->ozon()->accounts()->_new()->setClientId($fields['client_id'])->setApiKey($fields['api_key']);

			$this->db()->begin();

			if (!$this->ozon()->accounts()->update($item->getId(), $n->toArray())) {
				$this->db()->rollback();
				return $result->setError('Ошибка обновления');
			}

			// Блокировки
			$a = $this->ozon()->accounts()->_newActivity()->setBlockedFor($blockedFor);
			if (!$blockedFor) $a->setBlockedReason(false)->setBlockedSince(false)->setBlockedUntil(false);
			if (!$this->ozon()->accounts()->updateActivity($item->getId(), $a->toArray())) {
				$this->db()->rollback();
				return $result->setError('Ошибка обновления');
			}

			$this->db()->commit();

			$result->setStatus(true)->setMessage(implode('<br/>', $messages));
		}

		return $result;
	}
}
