<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Finance\Entity\Setting;

class FinanceController extends AbstractControlController
{
	protected $module = 'finance';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'finance';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->addCss[] = '/admin/css/modules/finance.css'.Func::modifyTime('/admin/css/modules/finance.css');
		$this->addJs[] = '/admin/js/modules/finance.js'.Func::modifyTime('/admin/js/modules/finance.js');
	}

	public function settings()
	{
		$this->vars['settings'] = $this->finance()->settings()->gets(['order_by' => 'id']);
		$this->layout()->page()->setHeader('Список Настроек');
		$this->tpl()->template($this->module.'/index');
	}

	public function editSetting($id)
	{
		$item = $this->finance()->settings()->get($id);
		if(!$item) $this->show404();
		// Saving
		if ($this->_save($item))
			$item = $this->finance()->settings()->get($id);

		$this->vars['item'] = $item;

		$this->layout()->page()->setHeader($item->getLabel());
		$this->tpl()->template($this->module.'/edit');
	}

	public function createSetting()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$label = strtoupper($this->input()->post('label', true));
		if ($this->finance()->settings()->getByLabel($label)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Константа [ '.$label.' ] уже есть в системе';
			die(json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($label, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}
		$c = $this->finance()->settings()->_new()->setLabel($label);
		if (!$this->finance()->settings()->create($c)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die(json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['id'] = $c->getId();

		die(json_encode($this->ajaxResponse));
	}

	/**
	 * @param Setting
	 * @return boolean
	 */
	public function _save(Setting $item)
	{
		if ($this->input()->post('save')) {

			$errors = [];

			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['description','value','label']))
					unset($fields[$field]);

			if (!$fields['label']) $errors[] = 'Не указан Label';
			//if (!mb_strlen($fields['Value'])) $result->setError($result->getError() . 'Не указано значение' . '<br />');

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) return $this->_notify(implode('<br/>', $errors), false);

			//обновляем запись
			$c = $this->finance()->settings()->_new()
				->setLabel($fields['label'])
				->setValue($fields['value'])
				->setDescription($fields['description']);
			if ($this->finance()->settings()->update($item->getId(), $c->toArray()))
				return $this->_notify('Изменения успешно сохранены');
			else
				return $this->_notify('Ошибка обновления', false);
		}
	}

	public function deleteSetting()
	{
		if (!$this->input()->isAjax()) {
			die();
		}

		if ($this->finance()->settings()->delete(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}
}
