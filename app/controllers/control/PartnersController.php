<?php if (!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\DateTime;
use Entity\Filter;
use Entity\Result;
use News\Entity\Partner;
use News\Entity\Rubric;

class NewsController extends AbstractControlController
{
	protected $module = 'partners';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'partners';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'sort',
			'order_dir'	=> 'ASC',
		]);

		// Status
		if (!is_null($this->input()->get('status'))) {
			$v = strval($this->input()->get('status'));
			switch ($v) {
				case 'all':
					break;
				default:
					$filter->set('status', $v);
					break;
			}
		}

		// Rubric
		if (!is_null($this->input()->get('sphere_id'))) {
			$filter->set('sphere_id', $this->input()->get('sphere_id'));
		}

		// Search
		if (trim($this->input()->get('search'))) {
			$filter->set('search', trim($this->input()->get('search')));
		}

		$filter->formLimits();

		$this->vars['partners'] = $this->partners()->gets($filter);
		$filter->setTotal($this->partners()->getTotal());

		$this->vars['spheres'] = $this->partners()->getRubrics(new Filter());
		$this->vars['relations'] = $this->partners()->getRelations(array_keys($this->vars['partners']));

		$this->vars['filter'] = $filter;

		// Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('per_page'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN . '/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		// Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addCss[] = '/admin/css/modules/partners.css' . Func::modifyTime('/admin/css/modules/partners.css');
		$this->addJs[] = '/admin/js/modules/partners.js' . Func::modifyTime('/admin/js/modules/partners.js');
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->layout()->page()->setHeader('Список партнеров');
		$this->tpl()->template($this->module . '/index');
	}

	public function create()
	{
		$this->_create();
		$this->layout()->page()->setHeader('Новый партнер');
		$this->tpl()->template($this->module . '/create');
	}

	private function _create()
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) $errors[] = 'Не указано имя';

			// Если насобирали ошибки, то выход..
			if ($errors) return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->partners()->_new()->setName($post['name'])->setCreated(new DateTime());

			if (!$this->partners()->create($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . '/' . $this->module);
		}
	}

	public function delete()
	{
		if (!$this->input()->isAjax()) die();

		if ($this->partners()->delete(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->partners()->_new();
		$u->setStatus($value ? 'active' : 'hidden');

		// Обновление
		if (!$this->partners()->update($id, $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function edit($id = false)
	{
		$partner = $this->partners()->smartGet($id);
		if (!$partner) $this->show404();

		// Saving
		$save = $this->_save($partner);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success()) $partner = $this->partners()->smartGet($id);

		$this->vars['partner'] = $partner;
		$this->vars['spheres'] = $this->partners()->getRubrics(new Filter());
		$this->vars['selectedRubrics'] = $partner->getRubrics();
		$this->addJs[] = '/admin/js/ajaxupload.3.5.js';
		$this->addJs[] = '/admin/js/modules/partners.js' . Func::modifyTime('/admin/js/modules/partners.js');
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		$this->addCss[] = '/admin/css/modules/partners.css' . Func::modifyTime('/admin/css/modules/partners.css');
		$this->layout()->page()->setHeader('Редактирование партнера');
		$this->tpl()->template($this->module . '/edit');
	}

	/**
	 * @param Partner
	 * @return Result
	 */
	public function _save(Partner $partner)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$spheres = $this->input()->post('spheres') ?: [];

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field) {
				if (!in_array($field, ['name','contact_name','contact_email','contact_telegram','benefit','description','site_name','site_link','phone','status','sphere']))
					unset($fields[$field]);
			}

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано имя' . '<br />');

			if (!empty($fields['site_link'])) {
				if (!filter_var($fields['site_link'], FILTER_VALIDATE_URL)) {
					$result->setError($result->getError() . 'Введите корректный Url' . '<br />');
				}
			}

			if (!empty($fields['contact_email'])) {
				if (!filter_var($fields['contact_email'], FILTER_VALIDATE_EMAIL)) {
					$result->setError($result->getError() . 'Введите корректный Email' . '<br />');
				}
			}
			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->partners()->_new()
				->setName($fields['name'])
				->setStatus($fields['status'])
				->setContactName($fields['contact_name'])
				->setContactEmail($fields['contact_email'])
				->setContactTelegram($fields['contact_telegram'])
				->setBenefit($fields['benefit'])
				->setDescription($fields['description'])
				->setPhone($fields['phone'])
				->setSiteLink($fields['site_link'])
				->setSiteName($fields['site_name']);

			// обновляем
			$this->db()->begin();

			if (!$this->partners()->update($partner->getId(), $n->toArray())) {
				$this->db()->rollback();
				return $result->setError('Ошибка обновления');
			}

			if (!$this->partners()->setRubrics($partner->getId(), $spheres)) {
				$this->db()->rollback();
				return $result->setError('Ошибка обновления');
			}

			$this->db()->commit();
			// -END обновляем

			$result->setStatus(true)->setMessage('Изменения успешно сохранены');
		}
		return $result;
	}

	public function setSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=', '', str_replace(' ', '', $str)));
		$sort = 0;
		foreach ($split as $id) {
			$sort = $sort + 5;
			$n = $this->partners()->_new()->setSort($sort);
			$this->partners()->update($id, $n->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function uploadPhoto()
	{
		$blogId = $this->input()->post('id', true);
		$partner = $this->partners()->get($blogId);
		if (!$partner) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		//Определяем параметры загрузки Картинки
		$upl = new Upload(
			[
				'upload_path' => VF_TMP_DIR,
				'allowed_types' => 'jpg|jpeg|png|bmp',
				'max_size' => '10120',
				'overwrite' => true,
			]
		);

		if ($upl->do_upload('file')) {
			$upInfo = $upl->data();
			$fileName = str_pad($partner->getId(), 3, '0', STR_PAD_LEFT) . '_' . md5(microtime(1)) . strtolower(
					$upInfo['file_ext']
				);    //Имя конечного файла
			$filePath = PARTNER_PATH . '/' . $fileName; //Полный путь

			// Переносим/переименовываем файл
			if (@rename($upInfo['full_path'], $filePath)) {
				$n = $this->partners()->_new()->setPhoto(PARTNER_URL . '/' . $fileName);
				if ($this->partners()->update($partner->getId(), $n->toArray())) {
					$this->ajaxResponse['descr'] = 'Файл успешно добавлен';
					$this->ajaxResponse['src'] = PARTNER_URL . '/' . $fileName;
					$this->ajaxResponse['del'] = true;
					// Unlink old photo
					if ($partner->getPhoto() && is_file(VF_PUBLIC_DIR . $partner->getPhoto())) {
						@unlink(VF_PUBLIC_DIR . $partner->getPhoto());
					}
				} else {
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}

				die (json_encode($this->ajaxResponse));
			} else {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка переноса файла';
				die (json_encode($this->ajaxResponse));
			}
		} elseif ($upl->display_errors()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $upl->display_errors('', '');//'Ошибка загрузки файла';
			die (json_encode($this->ajaxResponse));
		}

		die (json_encode($this->ajaxResponse));
	}

	public function deletePhoto()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}

		$partnerId = $this->input()->post('id', true);
		$partner = $this->partners()->get($partnerId);
		if (!$partner) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->partners()->_new()->setPhoto(false);
		if ($this->partners()->update($partner->getId(), $n->toArray())) {
			$this->ajaxResponse['descr'] = 'Файл удален';
			$this->ajaxResponse['del'] = false;
			$this->ajaxResponse['src'] = PARTNER_DEFAULT_PHOTO;
			// Unlink photo
			if ($partner->getPhoto() && is_file(VF_PUBLIC_DIR . $partner->getPhoto())) {
				@unlink(VF_PUBLIC_DIR . $partner->getPhoto());
			}
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка сохранения';
		}

		die (json_encode($this->ajaxResponse));
	}

	// SPHERES START
	public function spheres()
	{
		//Фильтрация
		$filter = new Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'sphere_id',
			'order_dir'	=> 'ASC',
		]);

		// Status
		if (!is_null($this->input()->get('status'))) {
			$v = strval($this->input()->get('status'));
			switch ($v) {
				case 'all':
					break;
				default:
					$filter->set('status', $v);
					break;
			}
		}

		// Search
		if (trim($this->input()->get('search'))) {
			$filter->set('search', trim($this->input()->get('search')));
		}

		$this->vars['filter'] = $filter;

		$this->vars['spheres'] = $this->partners()->getRubrics($filter);
		$filter->setTotal($this->partners()->getTotal());

		// Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('page'),
			'perPage'	=> $filter->get('per_page'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN . '/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		// Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addCss[] = '/admin/css/modules/partners.css' . Func::modifyTime('/admin/css/modules/partners.css');
		$this->addJs[] = '/admin/js/modules/partners.js' . Func::modifyTime('/admin/js/modules/partners.js');
		$this->layout()->page()->setHeader('Список сфер');
		$this->tpl()->template($this->module . '/spheres/index');
	}

	public function toggleRubrics()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}

		$id = intval($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->partners()->_newRubric();
		$u->setStatus($value ? 'active' : 'hidden');

		// Обновление
		if (!$this->partners()->updateRubric($id, $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function createRubric()
	{
		$this->_createRubric();
		$this->layout()->page()->setHeader('Новая сфера');
		$this->tpl()->template($this->module . '/spheres/create');
	}

	public function editRubric($id = false)
	{
		$sphere = $this->partners()->getRubric($id);
		if (!$sphere) {
			$this->show404();
		}

		// Saving
		$save = $this->_saveRubric($sphere);
		if ($save->getMessage()) {
			$this->_notify($save->getMessage());
		} elseif ($save->getError()) {
			$this->_notify($save->getError(), false);
		}
		if ($save->success()) {
			$sphere = $this->partners()->getRubric($id);
		}

		$this->vars['sphere'] = $sphere;

		$this->addJs[] = '/admin/js/modules/partners.js' . Func::modifyTime('/admin/js/modules/partners.js');
		$this->addCss[] = '/admin/css/modules/partners.css' . Func::modifyTime('/admin/css/modules/partners.css');

		$this->layout()->page()->setHeader('Редактирование сферы');
		$this->tpl()->template($this->module . '/spheres/edit');
	}

	/**
	 * @param Rubric
	 * @return Result
	 */
	public function _saveRubric(Rubric $sphere)
	{
		$result = new Result();
		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field) {
				if (!in_array(
					$field,
					[
						'name',
						'status'
					]
				)) {
					unset($fields[$field]);
				}
			}

			if (!$fields['name']) {
				$result->setError($result->getError() . 'Не указано имя' . '<br />');
			}
			if (!$fields['status']) {
				$result->setError($result->getError() . 'Не указано сортировка' . '<br />');
			}
			// Если насобирали ошибки, то выход..
			if ($result->getError()) {
				return $result;
			}

			$n = $this->partners()->_new()->setName($fields['name'])->setStatus($fields['status']);

			// обновляем запись
			if ($this->partners()->updateRubric($sphere->getId(), $n->toArray())) {
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			} else {
				$result->setError('Ошибка обновления');
			}
		}

		return $result;
	}

	private function _createRubric()
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) {
				$errors[] = 'Не указано имя';
			}

			$post['status'] = trim($post['status']);
			if (!$post['status']) {
				$errors[] = 'Не указан статус';
			}

			// Если насобирали ошибки, то выход..
			if ($errors) {
				return $this->_notify(implode('<br/>', $errors), false);
			}

			// добавляем запись
			$item = $this->partners()->_newRubric()->setName($post['name'])->setStatus($post['status']);

			if (!$this->partners()->createRubric($item)) {
				return $this->_notify('Ошибка создания', false);
			}

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . '/' . $this->module . '/spheres');
		}
	}

	public function deleteRubric()
	{
		if (!$this->input()->isAjax()) {
			die();
		}

		if ($this->partners()->deleteRubric(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}
}
