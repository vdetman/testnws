<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Layout\Entity\Snipet;

class SnipetsController extends AbstractControlController
{
	protected $module = 'snipets';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'snipets';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		$this->vars['snipets'] = $this->layout()->snipets()->gets();
		$this->addCss[] = '/admin/css/modules/snipets.css'.Func::modifyTime('/admin/css/modules/snipets.css');
		$this->addJs[] = '/admin/js/modules/snipets.js'.Func::modifyTime('/admin/js/modules/snipets.js');
		$this->layout()->page()->setHeader('Список сниппетов');
		$this->tpl()->template($this->module.'/index');
	}

	public function create()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = strtolower($this->input()->post('ident', true));
		if ($this->layout()->snipets()->get($ident)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Сниппет с идентификатором [ '.$ident.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($ident, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$s = $this->layout()->snipets()->_new()->setIdent($ident);
		if (!$this->layout()->snipets()->create($s)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
		}

		$this->_notify("Сниппет `{$ident}` успешно создан");

		die (json_encode($this->ajaxResponse));
	}

	public function delete()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$snipet = $this->layout()->snipets()->get(strtolower($this->input()->post('ident', true)));
		if (!$snipet) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Сниппет не найден';
			die (json_encode($this->ajaxResponse));
		}

		if ('required' == $snipet->getType()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить системный сниппет';
			die (json_encode($this->ajaxResponse));
		}

		if ($this->layout()->snipets()->delete($snipet->getIdent())) {
			$this->ajaxResponse['descr'] = 'Сниппет удален успешно';
		}else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления Сниппета';
		}

		die (json_encode($this->ajaxResponse));
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = strtolower($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		$snipet = $this->layout()->snipets()->get($ident);
		if (!$snipet) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Сниппет не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($field, ['Mode', 'status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->layout()->snipets()->_new();
		switch ($field) {
			case 'Mode': $u->setMode($value ? 'visual' : 'normal'); break;
			case 'status': $u->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->layout()->snipets()->update($snipet->getIdent(), $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	//Редактирование
	public function edit($ident = false)
	{
		$snipet = $this->layout()->snipets()->get($ident);
		if(!$snipet) $this->show404();

		// Saving
		if ($this->_save($snipet))
			$snipet = $this->layout()->snipets()->get($ident);

		$this->vars['snipet'] = $snipet;

		$this->addCss[] = '/admin/css/modules/snipets.css'.Func::modifyTime('/admin/css/modules/snipets.css');

		if ('visual' == $snipet->getMode()) {
			$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
			$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
			$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		}

		$this->layout()->page()->setHeader('Редактирование сниппета');
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Snipet
	 * @return boolean
	 */
	public function _save(Snipet $snipet)
	{
		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['description','Content']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			//обновляем запись
			$c = $this->layout()->snipets()->_new()->setContent($fields['Content'])->setDescription($fields['description']);
			if ($this->layout()->snipets()->update($snipet->getIdent(), $c->toArray()))
				return $this->_notify('Изменения успешно сохранены');
			else
				return $this->_notify('Ошибка обновления', false);

		}
	}
}