<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Layout\Entity\Constant;

class ConstantsController extends AbstractControlController
{
	protected $module = 'constants';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'constants';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->addCss[] = '/admin/css/modules/constants.css'.Func::modifyTime('/admin/css/modules/constants.css');
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addJs[] = '/admin/js/modules/constants.js'.Func::modifyTime('/admin/js/modules/constants.js');
	}

	public function index()
	{
		$this->vars['constants'] = $this->layout()->constants()->gets(['order_by' => 'sort']);
		$this->layout()->page()->setHeader('Список констант');
		$this->tpl()->template($this->module.'/index');
	}

	public function create()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$name = strtoupper($this->input()->post('name', true));
		if ($this->layout()->constants()->getByName($name)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Константа [ '.$name.' ] уже есть в системе';
			die(json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($name, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$c = $this->layout()->constants()->_new()->setName($name);
		if (!$this->layout()->constants()->create($c)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die(json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['id'] = $c->getId();

		die(json_encode($this->ajaxResponse));
	}

	public function edit($id = false)
	{
		$item = $this->layout()->constants()->get($id);
		if(!$item) $this->show404();

		// Saving
		if ($this->_save($item))
			$item = $this->layout()->constants()->get($id);

		$this->vars['item'] = $item;

		$this->layout()->page()->setHeader($item->getName());
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Constant
	 * @return boolean
	 */
	public function _save(Constant $item)
	{
		if ($this->input()->post('save')) {

			$errors = [];

			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['description','Value','Type']))
					unset($fields[$field]);

			if (!$fields['Type']) $errors[] = 'Не указан Type';
			//if (!mb_strlen($fields['Value'])) $result->setError($result->getError() . 'Не указано значение' . '<br />');

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) return $this->_notify(implode('<br/>', $errors), false);

			//обновляем запись
			$c = $this->layout()->constants()->_new()
				->setType($fields['Type'])
				->setValue($fields['Value'])
				->setDescription($fields['description']);
			if ($this->layout()->constants()->update($item->getId(), $c->toArray()))
				return $this->_notify('Изменения успешно сохранены');
			else
				return $this->_notify('Ошибка обновления', false);
		}
	}

	public function setSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=','',str_replace(' ','',$str)));
		$sort = 0;
		$updates = [];
		foreach($split as $id){
			$sort = $sort + 5;
			$updates[$id] = $this->layout()->constants()->_new()->setSort($sort)->toArray();
		}
		$this->layout()->constants()->multiUpdate($updates);
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function delete()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		if (!in_array($this->currentUser()->getRole(), ['root'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Нет полномочий';
			die(json_encode($this->ajaxResponse));
		}

		if (!$this->layout()->constants()->delete(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
			die(json_encode($this->ajaxResponse));
		}

		$this->_notify('Константа удалена');

		die(json_encode($this->ajaxResponse));
	}
}