<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Result;
use Entity\Filter;
use Entity\DateTime;
use Projects\Entity\Project as Item;

class ProjectsController extends AbstractControlController
{
	protected $module = 'projects';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	private function _modules()
	{
		$mods = [
			MODULE_ANALYTICS_ID			=>	['s' => 'API', 'n' => 'Аналитика'],
			MODULE_COMPETITORS_ID		=>	['s' => 'Кон', 'n' => 'Конкуренты'],
			MODULE_TOP_ID				=>	['s' => 'Кат', 'n' => 'Анализ категорий'],
			MODULE_ANALYSIS_ID			=>	['s' => 'Тов', 'n' => 'Анализ товара'],
			MODULE_POSITIONS_ID			=>	['s' => 'Поз', 'n' => 'Анализ позиций'],
			MODULE_EXTENSION_ID			=>	['s' => 'EXT', 'n' => 'Расширение'],
			MODULE_PRICE_EDITOR_ID		=>	['s' => 'РЦ', 'n' => 'Редактор цен'],
			MODULE_CARD_EDITOR_ID		=>	['s' => 'РКТ', 'n' => 'Редактор КТ'],
			MODULE_SALES_ANALYSIS_ID	=>	['s' => 'АП', 'n' => 'Анализ продаж'],
			MODULE_ASSISTANT_ID			=>	['s' => 'Асс', 'n' => 'Ассистент']
		];
		return $mods;
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'page'		=> $this->input()->get('page') ?: 1,
			'per_page'	=> ADMIN_PERPAGE,
			'order_by'	=> 'project_id',
			'order_dir'	=> 'DESC',
		]);

		// orderBy
		if (!is_null($orderBy = $this->input()->get('order', true))) {
			$filter->set('order', $orderBy);
			switch($orderBy){
				case 'id_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'project_id'); break;
				case 'id_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'project_id'); break;
			}
		}

		// MarketplaceId
		if (!is_null($this->input()->get('marketplace_id'))) {
			switch ($this->input()->get('marketplace_id')) {
				case 'all':break;
				default: $filter->set('marketplace_id', $this->input()->get('marketplace_id')); break;
			}
		}

		// TestPeriodFor
		if (!is_null($this->input()->get('TestPeriod'))) {
			$filter->set('TestPeriod', $this->input()->get('TestPeriod'));
			switch ($this->input()->get('TestPeriod')) {
				case 'all':break;
				case 'any': $filter->set('TestPeriodFor', [
					MODULE_ANALYTICS_ID,
					MODULE_COMPETITORS_ID,
					MODULE_TOP_ID,
					MODULE_ANALYSIS_ID,
					MODULE_POSITIONS_ID,
					MODULE_EXTENSION_ID,
					MODULE_PRICE_EDITOR_ID,
					MODULE_CARD_EDITOR_ID,
					MODULE_SALES_ANALYSIS_ID,
					MODULE_ASSISTANT_ID
				]); break;
				default: $filter->set('TestPeriodFor', [$this->input()->get('TestPeriod')]); break;
			}
		}

		// ProjectId
		if ($this->input()->get('project_id'))
			$filter->set('project_id', $this->input()->get('project_id'));

		// Status
		if (!is_null($this->input()->get('status'))) {
			switch($this->input()->get('status')){
				case 'all':break;
				default: $filter->set('status', $this->input()->get('status')); break;
			}
		}

		// Created
		if (trim($this->input()->get('since'))) $filter->set('since', date('Y-m-d', strtotime(trim($this->input()->get('since')))));
		if (trim($this->input()->get('until'))) $filter->set('until', date('Y-m-d', strtotime(trim($this->input()->get('until')))));

		// Search
		if(trim($this->input()->get('search')) != '')
			$filter->set('search', trim($this->input()->get('search')));

		$filter->formLimits();
		$this->vars['list'] = $this->projects()->common()->gets($filter);
		//echo '<pre>',print_r($this->vars['list'], 1),'</pre>'; die();
		$filter->setTotal( $this->projects()->common()->getTotal());

		$this->vars['mods'] = $this->_modules();

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

		$this->addJs[] = '/admin/js/modules/projects.js'.Func::modifyTime('/admin/js/modules/projects.js');
		$this->addCss[] = '/admin/css/modules/projects.css'.Func::modifyTime('/admin/css/modules/projects.css');

		$this->layout()->page()->setHeader('Проекты');
		$this->tpl()->template($this->module.'/index');
	}

	public function edit($id = false)
	{
		if (false == ($item = $this->projects()->common()->get($id))) $this->show404();

		// Saving
		$result = $this->_save($item);
		if ($result->getError() || $result->getMessage()) $this->_notify($result->getError()?:$result->getMessage(), $result->success());
		if ($result->success()) Func::redirect(ADMIN . "/{$this->module}/edit/{$item->getId()}");

		$this->vars['item'] = $item;
		$this->vars['mods'] = $this->_modules();

		// Получим активированные тестовые периоды в проектах пользователя
		$existPeriods = $this->existTestPeriods($item);

		$restrictModules = $projectPeriods = [];
		foreach ($existPeriods as $period){
			if($period->getProjectId() != $item->getId())
				$restrictModules[$period->getModuleId()] = $period->getModuleId(); // Список запрещённых модулей для установки тестового периода
		}

		foreach ($this->marketplaces()->getModules(new Filter(['status'=>'active'])) as $module){
			if(!in_array( $module->getId(), $restrictModules)){
				$modules[$module->getId()] = $module;
			}
		}

		// Доступные модули для установки тестового периода на данном проекте
		$this->vars['modules'] = $modules;

		$this->addJs[] = '/admin/js/jquery.autocomplete.min.js';
		$this->addJs[] = '/admin/js/modules/projects.js'.Func::modifyTime('/admin/js/modules/projects.js');

		$this->layout()->page()->setHeader($item->getName() . '. <i>(' . $item->getMarketplace()->getName() . ')</i>');
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Item
	 * @return Result
	 */
	public function _save(Item $item)
	{
		$errors = $messages = [];
		$hasChangedOwner = false;
		$result = new Result();

		if($this->input()->post('save'))
		{
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['name','status','user_id','User']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			if (!$fields['name']) $errors[] = 'Не указано название';
			if (!in_array($fields['status'], ['active', 'deleted'])) $errors[] = 'Не указан статус';

			$newOwner = null;
			if ($fields['user_id']) {
				$newOwner = $this->users()->get($fields['user_id']);
				if (!$newOwner) $errors[] = 'Не найден новый владелец';
				if ($newOwner->getId() == $item->getUserId()) $errors[] = 'Указанный пользователь уже является владелцем';
				$fields['user_id'] = intval($newOwner->getId());
			}

			// Если насобирали ошибки, то выход..
			if ($errors) return $result->setError(implode('<br/>', $errors));

			$messages[] = 'Изменения успешно сохранены';

			// Updating
			$n = $this->projects()->common()->_new()->setName($fields['name']);

			if ($fields['status'] != $item->getStatus())
				$n->setStatus($fields['status'])->setDeleted('active' == $fields['status'] ? false : new DateTime());

			// Смена владельца
			if ($fields['user_id']) {
				$hasChangedOwner = true;
				$n->setUserId($fields['user_id']);
				$messages[] = 'Владелец проекта изменен';
			}

			$this->db()->begin();

			if (!$this->projects()->common()->update($item->getId(), $n->toArray())) {
				$this->db()->rollback();
				return $result->setError('Ошибка обновления');
			}

			if ($hasChangedOwner) {
				// Проверяем доступ к этому проекту у нового владельца, удаляем, если есть
				$access = $this->projects()->accesses()->get($item->getId(), $newOwner->getId());
				if ($access) {
					$dResult = $this->projects()->accesses()->delete($access->getId(), $access->getUserId());
					if (!$dResult->success()) {
						$this->db()->rollback();
						return $result->setError($dResult->getError());
					}
				}
			}

			$this->db()->commit();

			if ($hasChangedOwner) {
				$this->cache()->deleteByTag(CACHE_TAG_USER_ . $n->getUserId());
				$this->cache()->deleteByTag(CACHE_TAG_USER_ . $item->getUserId());
			}

			$result->setStatus(true)->setMessage(implode('<br/>', $messages));
		}

		return $result;
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));
		$field = strval($this->input()->post('field', true));
		$value = intval($this->input()->post('value', true));

		$item = $this->projects()->common()->get($id);
		if (!$item) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Недопустимое поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($value, [0, 1])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Недопустимое значение: {$value}";
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->projects()->common()->_new();
		switch ($field) {
			case 'status': $n->setStatus($value == 1 ? 'active' : 'deleted')->setDeleted($value == 1 ? false : new DateTime()); break;
		}

		//Обновление
		if (!$this->projects()->common()->update($item->getId(), $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';
		$this->ajaxResponse['item'] = $this->tpl()->get($this->module.'/_item', ['i' => $this->projects()->common()->get($item->getId()), 'module' => $this->module]);

		die (json_encode($this->ajaxResponse));
	}

	public function setTestPeriod($id = false)
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		// Проверим существует ли проект
		if (false == ($item = $this->projects()->common()->get($id))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Проект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ($newPeriods = $this->input()->post('testPeriods')) {

			//Получим активированные тестовые периоды в проектах пользователя
			$existPeriods = $this->existTestPeriods($item);

			$restrictModules = $projectPeriods = [];
			foreach ($existPeriods as $moduleId => $period) {
				if($period->getProjectId() != $item->getId())
					$restrictModules[$period->getModuleId()] = $period->getModuleId(); // Список модулей на которые действует тестовый период на других проектах
				else
					$projectPeriods[$period->getModuleId()] = $period->getModuleId(); // Список модулей на которые действует тестовый период на данном проекте
			}

			// Удалим из изменяемых периодов те, которые есть на других проектах
			$newPeriods = array_diff_key($newPeriods, $restrictModules);

			// Получаем существующие модули на сайте
			$modules = [];
			foreach ($this->marketplaces()->getModules(new Filter(['status'=>'active'])) as $module)
				$modules[$module->getId()] = $module;

			// удалим из изменяемых периодов модули которые не существуют
			$newPeriods = array_intersect_key($newPeriods, $modules);

			if (!count($newPeriods)) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = "Не найдены тестовые периоды подлежащие изменению";
				die (json_encode($this->ajaxResponse));
			}

			foreach ($newPeriods as $moduleId => $expires) {
				if (isset($existPeriods[$moduleId])) {
					$existPeriod = $existPeriods[$moduleId];
					if ($expires) {
						$up = $this->projects()->testPeriods()->_new()->setExpires(new DateTime(date('Y-m-d 23:59:59', strtotime($expires))));
						$this->projects()->testPeriods()->update($existPeriod->getId(), $up->toArray());
						continue;
					}
					// Remove TP
					$this->projects()->testPeriods()->delete($existPeriod->getId());
					continue;
				}
				if (!$expires) {
					continue;
				}
				$period = $this->projects()->testPeriods()->_new()
					->setUserId($item->getUserId())
					->setProjectId($item->getId())
					->setMarketplaceId($item->getMarketplaceId())
					->setModuleId($moduleId)
					->setExpires(new DateTime(date('Y-m-d 23:59:59', strtotime($expires))));
				if ($period->getExpires())
					$this->projects()->testPeriods()->create($period);
			}
			$this->ajaxResponse['status'] = true;
			$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

			die (json_encode($this->ajaxResponse));
		}
	}

	private function existTestPeriods(\Projects\Entity\Project $project)
	{
		$result = [];

		$filter = new Filter();
		$filter->set('user_id', $project->getUserId())
			->set('marketplace_id', $project->getMarketplaceId());

		// Получаем все тестовые периоды пользователя
		$userTestPeriods = $this->projects()->testPeriods()->gets($filter);

		foreach ($userTestPeriods as $period)
			$result[$period->getModuleId()] = $period;

		return $result;
	}

}
