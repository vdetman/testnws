<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Result;
use Entity\Filter;
use Entity\DateTime;
use Wb\Accounts\Entity\Account as Item;

class WildberriesController extends AbstractControlController
{
	protected $module = 'wildberries';

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
		$this->vars['list'] = $this->wb()->accounts()->gets($filter);
		//echo '<pre>',print_r($this->vars['list'], 1),'</pre>'; die();
		$filter->setTotal($this->wb()->accounts()->getTotal());

		//echo '<pre>',print_r($this->vars['list'], 1),'</pre>'; die();

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

		$this->addJs[] = '/admin/js/modules/wildberries.js'.Func::modifyTime('/admin/js/modules/wildberries.js');
		$this->addCss[] = '/admin/css/modules/wildberries.css'.Func::modifyTime('/admin/css/modules/wildberries.css');

		$this->layout()->page()->setHeader('Аккаунты Wildberries');
		$this->tpl()->template($this->module.'/index');
	}

	public function edit($id = false)
	{
		if (false == ($item = $this->wb()->accounts()->get($id))) $this->show404();

		//echo '<pre>',print_r($item, 1),'</pre>'; die();

		// Saving
		$result = $this->_save($item);
		if ($result->getError() || $result->getMessage()) $this->_notify($result->getError()?:$result->getMessage(), $result->success());
		if ($result->success()) Func::redirect(ADMIN . "/{$this->module}/edit/{$item->getId()}");

		$this->vars['item'] = $item;

		//$this->vars['blocked'] = $item->getActivity()->getBlockedFor() ? explode(',', $item->getActivity()->getBlockedFor()) : [];

		$this->addJs[] = '/admin/js/modules/wildberries.js'.Func::modifyTime('/admin/js/modules/wildberries.js');

		$this->layout()->page()->setHeader('Wb account #' . $item->getId());
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Item
	 * @return Result
	 */
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
				if(!in_array($field, ['api_key','api_key_v2'/*,'Blocked'*/]))
					unset($fields[$field]);

			$apiKey = trim($fields['api_key']);
			if ($apiKey) {
				if (!$this->wb()->accounts()->isValidKey($apiKey))
					$errors[] = 'ApiKey имеет неверный формат';

				if ($this->wb()->accounts()->existKey($apiKey, $item->getId()))
					$errors[] = 'Аккаунт с таким ApiKey уже был добавлен ранее';
			}
			$fields['api_key'] = $apiKey;

			$apiKeyV2 = trim($fields['api_key_v2']);
			if ($apiKeyV2) {
				if (!$this->wb()->accounts()->isValidKey($apiKeyV2, true))
					$errors[] = 'ApiKey V2 имеет неверный формат';

				if ($this->wb()->accounts()->existKey($apiKeyV2, $item->getId(), true))
					$errors[] = 'Аккаунт с таким ApiKey V2 уже был добавлен ранее';
			}
			$fields['api_key_v2'] = $apiKeyV2;

			// Блокировки
//			$blockedFor = !empty($fields["Blocked"])
//				? implode(',', array_intersect($fields["Blocked"], ['stocks', 'orders', 'sales', 'prices', 'incomes', 'all']))
//				: false;

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) return $result->setError(implode('<br/>', $errors));

			$messages[] = 'Изменения успешно сохранены';

			// Updating
			$n = $this->wb()->accounts()->_newKeys()
				->setApiKey($apiKey ?: false)
				->setApiKeyV2($apiKeyV2 ?: false);

			if (!$this->wb()->accounts()->updateKeys($item->getId(), $n->toArray()))
				return $result->setError('Ошибка обновления');

			$result->setStatus(true)->setMessage(implode('<br/>', $messages));
		}

		return $result;
	}
}