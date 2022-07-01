<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use Helper\Text;

//Модуль управления разделами сайта
class PagesController extends AbstractControlController
{
	protected $module = 'pages';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'search';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		$this->vars['pages'] = $this->layout()->pages()->gets(new Filter());

		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addJs[] = '/admin/js/modules/pages.js'.Func::modifyTime('/admin/js/modules/pages.js');
		$this->addCss[] = '/admin/css/modules/pages.css'.Func::modifyTime('/admin/css/modules/pages.css');

		$this->layout()->page()->setHeader('Страницы сайта');
		$this->tpl()->template($this->module.'/index');
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));
		$field = trim($this->input()->post('field', true));
		$value = intval($this->input()->post('value', true));

		if(!in_array($field, ['status'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимое поле: ' . $field;
			die (json_encode($this->ajaxResponse));
		}

		if(!in_array($value, [0, 1])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение данных: ' . $value;
			die (json_encode($this->ajaxResponse));
		}

		$p = $this->layout()->pages()->_new();

		switch ($field) {
			case 'status':
				if (in_array($this->layout()->pages()->get($id)->getAlias(), ['main','p404']) && !$value) {
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Невозможно отключить этот раздел';
					die (json_encode($this->ajaxResponse));
				}
				$p->setStatus($value ? 'active' : 'hidden');
				break;
		}

		// Обновление
		if (!$this->layout()->pages()->update($id, $p->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

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
			$this->layout()->pages()->update($id, $this->layout()->pages()->_new()->setSort($sort)->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	// Редактирование
	public function edit($pageId = false)
	{
		$page = $this->layout()->pages()->get($pageId);
		if(!$page) $this->show404();

		// Result
		$result = $this->_save($page);
		if ($result->success()) $page = $this->layout()->pages()->get($pageId);
		$this->vars['result'] = $result;

		// All languages
		$this->vars['languages'] = $this->languages()->getAll();

		// robots
		$this->vars['robots'] = ['','index, follow','noindex, nofollow','noindex, follow','index, nofollow'];

		$this->vars['page'] = $page;

		$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';

		$this->layout()->page()->setHeader($page->getName());
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * Сохранение
	 * @param \Layout\Entity\Page
	 * @return Result
	 */
	public function _save(\Layout\Entity\Page $page)
	{
		// Result
		$result = new \Entity\Result();

		if($this->input()->post('save'))
		{
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['name','Alias','status']))
					unset($fields[$field]);

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано название' . '<br />');

			$alias = \Helper\Text::translitUrl($fields['Alias']);
			if (!$alias)
				$result->setError($result->getError() . 'Не указан Алиас' . '<br />');
			else if (false != ($exist = $this->layout()->pages()->getByAlias($alias)) && $exist->getId() != $page->getId())
				$result->setError($result->getError() . 'Указанный Алиас уже есть в системе' . '<br />');

			//Не даем заменить alias Main и p404
			$fields['Alias'] = in_array($page->getAlias(), ['main','p404']) ? $page->getAlias() : $fields['Alias'];

			//Не даем заменить отключить Main и p404
			$fields['status'] = in_array($page->getAlias(), ['main','p404']) ? $page->getStatus() : $fields['status'];

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			// DATA
			$data = $this->input()->post('data') ?: [];
			foreach ($data as $lang => $d) {
				$d = array_map('trim', $d);
				$exist = $this->layout()->pages()->getData(new \Entity\Filter(['PageId' => $page->getId(), 'Language' => $lang]));
				$u = $this->layout()->pages()->_newData()->fromArray($d)->setPageId($page->getId())->setLanguage($lang);
				if (!$exist && !$this->layout()->pages()->createData($u)) {
					return $result->setError('Ошибка создания данных');
				} elseif ($exist && !$this->layout()->pages()->updateData($page->getId(), $lang, $u->toArray())) {
					return $result->setError('Ошибка обновления данных');
				}
			}

			//обновляем запись
			if ($this->layout()->pages()->update($page->getId(), $fields)) {
				$result->setStatus(true);
				$result->setMessage('Изменения успешно сохранены');
			} else {
				$result->setError('Ошибка обновления');
			}
		}

		return $result;
	}

	public function create()
	{
		$this->vars['result'] = $this->_add();
		$this->vars['alias'] = $this->layout()->pages()->getUniqueAlias();
		$this->layout()->page()->setHeader('Создание страницы');
		$this->tpl()->template($this->module.'/create');
	}

	/**
	 * @return Result
	 */
	private function _add()
	{
		// Result
		$result = new Result();

		if($this->input()->post('submit'))
		{
			// POST
			$fields = $this->input()->post('field', true);
			$fields = array_map('trim', $fields);

			// Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['Alias','name']))
					unset($fields[$field]);

			if(!$fields['name'])
				$result->setError($result->getError() . 'Не указано название' . '<br />');

			$alias = Text::translitUrl($fields['Alias']);
			if (!$alias)
				$result->setError($result->getError() . 'Не указан Алиас' . '<br />');
			else if ($this->layout()->pages()->getByAlias($alias))
				$result->setError($result->getError() . 'Указанный Алиас уже есть в системе' . '<br />');
			$fields['Alias'] = $alias;

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			//добавляем запись
			$page = $this->layout()->pages()->_new()->setAlias($fields['Alias'])->setName($fields['name']);
			if ($this->layout()->pages()->create($page))
				Func::redirect(ADMIN.'/'.$this->module.'/edit/'.$page->getId());
			else
				$result->setError('Ошибка создания');

			$result->setStatus(true);
		}

		return $result;
	}

	// Удаление
	public function delete()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$page = $this->layout()->pages()->get(intval($this->input()->post('id', true)));
		if(!$page){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if(in_array($page->getAlias(), ['main','p404'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить этот раздел';
			die (json_encode($this->ajaxResponse));
		}

		if ($this->layout()->pages()->delete($page)) {
			$this->ajaxResponse['descr'] = 'Элемент успешно удален';
		}else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		die (json_encode($this->ajaxResponse));
	}
}