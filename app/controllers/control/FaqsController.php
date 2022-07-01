<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use \Faqs\Entity\Faq;

class FaqsController extends AbstractControlController
{
	protected $module = 'faqs';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'faqs';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->addCss[] = '/admin/css/modules/faqs.css'.Func::modifyTime('/admin/css/modules/faqs.css');
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addJs[] = '/admin/js/modules/faqs.js'.Func::modifyTime('/admin/js/modules/faqs.js');
	}

// Faqs
	public function index()
	{
		$this->vars['faqs'] = $this->Faqs()->gets(new Filter());
		//echo '<pre>',print_r($this->vars['faqs'], 1),'</pre>'; die();
		$this->layout()->page()->setHeader('Блоки FAQ');
		$this->tpl()->template($this->module.'/index');
	}

	public function createFaq()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = mb_strtoupper(strval($this->input()->post('ident', true)));
		if (!$ident) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не указан идентификатор';
			die (json_encode($this->ajaxResponse));
		}

		if($this->faqs()->getByIdent($ident)){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент с таким идентификатором уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($ident))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$faq = $this->faqs()->_newFaq()->setIdent($ident);

		if (!$this->faqs()->create($faq)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['redirect'] = '/' . ADMIN . '/' . $this->module . '/edit/' . $faq->getId();
		$this->_notify("Элемент {$faq->getIdent()} успешно создан");

		die (json_encode($this->ajaxResponse));
	}

	public function setSortingFaq()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=','',str_replace(' ','',$str)));
		$sort = 0;
		foreach($split as $id){
			$sort = $sort + 5;
			$s = $this->faqs()->_newFaq()->setSort($sort);
			$this->faqs()->update($id, $s->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function toggleFaq()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$value = (int) $this->input()->post('value', true);

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение поля: ' . $field;
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($value, [0,1])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение данных: ' . $value;
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->faqs()->_newFaq();

		switch ($field) {
			case 'status': $u->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->faqs()->update($id, $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	// Удаление
	public function deleteFaq()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		if (false == ($faq = $this->faqs()->get(intval($this->input()->post('id', true))))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if ($this->faqs()->delete($faq)) {
			$this->ajaxResponse['descr'] = 'Элемент успешно удален';
		}else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		die (json_encode($this->ajaxResponse));
	}
// -END Faqs

// Items
	public function edit($faqId = false)
	{
		$faq = $this->faqs()->get($faqId);
		if (!$faq) return $this->show404();

		// Save
		$this->_save($faq);

		$this->vars['faq'] = $faq;

		// All languages
		$this->vars['languages'] = $this->languages()->getAll();

		$this->layout()->page()->setHeader('Блок ' . $faq->getIdent());
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Faq
	 * @return boolean
	 */
	public function _save(Faq $faq)
	{
		if($this->input()->post('save'))
		{
			$errors = [];

			// POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			// Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['name','status']))
					unset($fields[$field]);

			if (!$fields['name'])
				$errors[] = 'Не указано название';

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) {
				$this->_notify(implode('<br/>', $errors), false);
				return false;
			}

			// обновляем запись
			$u = $this->faqs()->_newFaq()->setName($fields['name'])->setStatus($fields['status']);
			if ($this->faqs()->update($faq->getId(), $u->toArray())) {
				$this->_notify('Изменения успешно сохранены');
				$faq->fromArray($u->toArray());
				return true;
			} else
				$this->_notify('Ошибка обновления', false);
		}
		return false;
	}

	public function createItem()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		if (false == ($faq = $this->faqs()->get(intval($this->input()->post('fid', true))))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->faqs()->_newItem()->setFaqId($faq->getId());
		if (!$this->faqs()->createItem($n)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['item'] = $this->tpl()->get($this->module.'/_item', [
			'item' => $n,
			'languages' => $this->languages()->getAll()
		]);
		$this->ajaxResponse['descr'] = 'Элемент успешно создан';

		die (json_encode($this->ajaxResponse));
	}

	public function toggleItem()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$value = (int) $this->input()->post('value', true);

		if (!in_array($field, ['status','IsVideo'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение поля: ' . $field;
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($value, [0,1])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение данных: ' . $value;
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->faqs()->_newItem();
		switch ($field) {
			case 'status': $u->setStatus($value ? 'active' : 'hidden'); break;
			case 'IsVideo': $u->setIsVideo($value); break;
		}

		// Обновление
		if (!$this->faqs()->updateItem($id, $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function deleteItem()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$item = $this->faqs()->getItem(intval($this->input()->post('id', true)));
		if (!$item) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->faqs()->deleteItem($item)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Элемент успешно удален';
		die (json_encode($this->ajaxResponse));
	}

	public function setSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=','',str_replace(' ','',$str)));
		$sort = 0;
		foreach($split as $id){
			$sort = $sort + 5;
			$s = $this->faqs()->_newItem()->setSort($sort);
			$this->faqs()->updateItem($id, $s->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function updateItem()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$field = trim($this->input()->post('field', true));
		$value = trim($this->input()->post('value', true));
		$lang = trim($this->input()->post('lang', true));

		$item = $this->faqs()->getItem(intval($this->input()->post('id', true)));
		if (!$item) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if(!in_array($field, ['Question','Answer','VideoCode'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение поля';
			die (json_encode($this->ajaxResponse));
		}

		// Обновление
		$exist = $this->faqs()->getData(new Filter(['ItemId' => $item->getId(), 'Language' => $lang]));
		$u = $this->faqs()->_newData()->setFaqId($item->getFaqId())->setItemId($item->getId())->setLanguage($lang)->{'set' . $field}($value);
		if (!$exist && !$this->faqs()->createData($u)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания данных';
			die (json_encode($this->ajaxResponse));
		} elseif ($exist && !$this->faqs()->updateData($item->getId(), $lang, $u->toArray())){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления данных';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';
		die (json_encode($this->ajaxResponse));
	}
// -END Items
}