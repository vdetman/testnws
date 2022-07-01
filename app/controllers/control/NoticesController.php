<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use Notices\Email\Entity\Letter;

class NoticesController extends AbstractControlController
{
	protected $module	= 'notices';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'notices';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function languages()
	{
		return [
			'ru' => 'Русский'
		];
	}

// Notices
	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'Page'		=> $this->input()->get('page') ?: 1,
			'PerPage'	=> ADMIN_PERPAGE,
			'order_by'	=> 'NoticeId',
			'order_dir'	=> 'DESC',
		]);

		// Order
		if(null !== ($order = $this->input()->get('Order', true))){
			$filter->set('Order', $order);
			switch($order){
				case 'id_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'NoticeId'); break;
				case 'id_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'NoticeId'); break;
			}
		}

		// Status
		if(null !== $this->input()->get('status')){
			$value = $this->input()->get('status');
			switch($value){
				case 'all':break;
				default: $filter->set('status', $value); break;
			}
		}

		// Type
		if(null !== $this->input()->get('Type')){
			$value = $this->input()->get('Type');
			switch($value){
				case 'all':break;
				default: $filter->set('Type', $value); break;
			}
		}

		// period
		if(trim($this->input()->get('PeriodMin')) != '')
			$filter->set('PeriodMin', date('Y-m-d', strtotime(trim($this->input()->get('PeriodMin')))));
		if(trim($this->input()->get('PeriodMax')) != '')
			$filter->set('PeriodMax', date('Y-m-d', strtotime(trim($this->input()->get('PeriodMax')))));

		// Search
		if (trim($this->input()->get('search')))
			$filter->set('search', trim($this->input()->get('search')));

		$this->vars['notices'] = $this->notices()->common()->gets($filter);
		//echo '<pre>',print_r($this->vars['notices'], 1),'</pre>'; die();

		$filter->setTotal($this->notices()->common()->getTotal());
		$this->vars['filter'] = $filter;

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('Page'),
			'perPage'	=> $filter->get('PerPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		$this->addCss[] = '/admin/css/modules/notices.css'.Func::modifyTime('/admin/css/modules/notices.css');
		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');

		$this->vars['currentMenu'] = 'index';
		$this->layout()->page()->setHeader('Логи отправки уведомлений');
		$this->tpl()->template($this->module.'/index');
	}

	public function resetNoticeError()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$notice = $this->notices()->common()->get(intval($this->input()->post('nid', true)));
		if (!$notice) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ('error' != $notice->getStatus()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не в статусе ERROR";
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->notices()->common()->_new()->setStatus('new')->setAttempts(false)->setExecuted(false)->setError(false);
		if (!$this->notices()->common()->update($notice->getId(), $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Ошибка обновления";
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = "Объект обновлен";
		$this->ajaxResponse['notice'] = $this->tpl()->get($this->module.'/_item', ['n' => $this->notices()->common()->get($notice->getId())]);

		die (json_encode($this->ajaxResponse));
	}

	public function executeNotice()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$notice = $this->notices()->common()->get(intval($this->input()->post('nid', true)));
		if (!$notice) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ('success' == $notice->getStatus()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект в статусе SUCCESS";
			die (json_encode($this->ajaxResponse));
		}

		// Execute!
		$this->notices()->forceExecute($notice);

		$this->ajaxResponse['descr'] = "Объект обновлен";
		$this->ajaxResponse['notice'] = $this->tpl()->get($this->module.'/_item', ['n' => $this->notices()->common()->get($notice->getId())]);

		die (json_encode($this->ajaxResponse));
	}

	public function showNoticeDetail()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/_detail', ['nid' => intval($this->input()->post('nid', true)), 'field' => strval($this->input()->post('field', true))]);
		die (json_encode($this->ajaxResponse));
	}

	public function showNoticeDetailView()
	{
		$notice = $this->notices()->common()->get(intval($this->input()->get('nid', true)));
		if (!$notice) die ("Объект не найден");

		$field = strval($this->input()->get('field', true));
		if (!in_array($field, ['Message','Error'])) die ("Неверное поле");

		echo print_r($notice->{'get' . $field}(), 1);
		die();
	}

	public function editNotice()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$notice = $this->notices()->common()->get(intval($this->input()->post('nid', true)));
		if (!$notice) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ('error' != $notice->getStatus()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не в статусе ERROR";
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/_edit', ['notice' => $notice]);
		die (json_encode($this->ajaxResponse));
	}

	public function saveNotice()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$notice = $this->notices()->common()->get(intval($this->input()->post('nid', true)));
		if (!$notice) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		if ('error' != $notice->getStatus()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не в статусе ERROR";
			die (json_encode($this->ajaxResponse));
		}

		$params = [];
		parse_str($this->input()->post('params'), $params);

		$generalData = $params['field'];
		$generalData = array_map('trim', $generalData);

		if (!$generalData['Recipient']) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Не указан получатель";
			die (json_encode($this->ajaxResponse));
		}

		if ('email' == $notice->getType() && !$generalData['Subject']) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Не указана тема письма";
			die (json_encode($this->ajaxResponse));
		}

		if (!$generalData['Message']) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Не указан текст сообщения";
			die (json_encode($this->ajaxResponse));
		}

		// обновляем запись
		if (!$this->notices()->common()->update($notice->getId(), $generalData)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Ошибка обновления";
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = "Изменения успешно сохранены";
		$this->ajaxResponse['notice'] = $this->tpl()->get($this->module.'/_item', ['n' => $this->notices()->common()->get($notice->getId())]);

		die (json_encode($this->ajaxResponse));
	}
// -END Notices

// Telegram
	public function telegram($tab = false)
	{
		if (!in_array($tab, ['templates','settings','bot','preparing','accounts'])) Func::redirect(ADMIN . '/' . $this->module . '/telegram/' . 'templates');

		switch ($tab) {
			case 'templates': $this->_pageTemplates();  break;
			case 'settings': $this->_pageSettings();  break;
			case 'preparing': $this->_pagePreparing();  break;
			case 'accounts': $this->_pageAccounts();  break;
			case 'bot': $this->_pageBot(); break;
		}

		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');
		$this->addCss[] = '/admin/css/modules/notices.css'.Func::modifyTime('/admin/css/modules/notices.css');

		$this->vars['currentTab'] = $tab;
		$this->vars['currentMenu'] = 'telegram';
		$this->layout()->page()->setHeader('Настройка Telegram');
		$this->tpl()->template($this->module.'/telegram/index');
	}

	private function _pageTemplates()
	{
		$this->vars['templates'] = $this->notices()->telegram()->templates()->gets(new Filter());
	}

	private function _pageSettings()
	{
		if ($this->input()->post('save')) {
			$fields = $this->input()->post('settings'); // POST

			Db::begin();

			$flag = true;
			foreach ($fields as $id => $value) {
				if (!$this->notices()->telegram()->settings()->update($id, $this->notices()->telegram()->settings()->_new()->setValue(trim($value))->toArray())) {
					$flag = false;
				}
			}

			if (!$flag) {
				$this->_notify('Ошибка обновления', false);
				Db::rollback();
			} else {
				$this->_notify('Изменения успешно сохранены');
				Db::commit();
			}
		}

		// Telegram settings
		$this->vars['settings'] = $this->notices()->telegram()->settings()->gets(new Filter());
	}

	private function _pagePreparing()
	{
		$this->vars['preparings'] = $this->notices()->telegram()->preparings()->gets();
	}

	private function _pageAccounts()
	{
		$this->vars['accounts'] = $this->notices()->telegram()->accounts()->gets(new Filter());
	}

	private function _pageBot()
	{
		// Telegram settings
		$this->vars['settings'] = $this->notices()->telegram()->settings()->gets(new Filter());
	}

	public function deleteTelegramPreparing()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$preparing = $this->notices()->telegram()->preparings()->getByUserId(intval($this->input()->post('id', true)));
		if (!$preparing) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Объект не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->telegram()->preparings()->delete($preparing->getUserId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		$this->ajaxResponse['descr'] = 'Объект удален успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function deleteTelegramAccount()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$account = $this->notices()->telegram()->accounts()->get(intval($this->input()->post('id', true)));
		if (!$account) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Объект не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->telegram()->accounts()->delete($account->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		$this->ajaxResponse['descr'] = 'Объект удален успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function deleteTelegramProperty()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$property = $this->notices()->telegram()->settings()->get(intval($this->input()->post('id', true)));
		if (!$property) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Объект не найден';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на использование аккаунта в уведомлениях
		if ($property->getRequired()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить это свойство';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->telegram()->settings()->delete($property->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		$this->ajaxResponse['descr'] = 'Свойство удалено успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function createTelegramProperty()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$property = strtoupper($this->input()->post('ident', true));

		if ($this->notices()->telegram()->settings()->getByProperty($property)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Свойство [ '.$property.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($property, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$e = $this->notices()->telegram()->settings()->_new()->setProperty($property);
		if (!$this->notices()->telegram()->settings()->create($e)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания События';
			die (json_encode($this->ajaxResponse));
		}

		$this->_notify('Свойство успешно создано');

		die (json_encode($this->ajaxResponse));
	}

	public function telegramPreRequest()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$method = trim($this->input()->post('method', true));
		if (!in_array($method, ['setWebhook','sendMessage'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Wrong method: {$method}";
			die (json_encode($this->ajaxResponse));
		}

		$params = [];
		switch ($method) {
			case 'setWebhook':
				$params = [[
					'type'			=> 'input',
					'label'			=> 'url',
					'name'			=> 'url',
					'placeholder'	=> 'HTTPS url to send updates to. Use an empty string to remove webhook integration',
					'help'			=> DOMAIN . '/api/telegramUpdates/{BOT_TOKEN}',
				]];
				break;
			case 'sendMessage':
				$params = [[
					'type'			=> 'input',
					'label'			=> 'Chat_id',
					'name'			=> 'chat_id',
					'placeholder'	=> 'Unique identifier for the target chat or username of the target channel',
					'help'			=> '',
				],[
					'type'			=> 'textarea',
					'label'			=> 'Message',
					'name'			=> 'text',
					'placeholder'	=> 'Text of the message to be sent, 1-4096 characters after entities parsing',
					'help'			=> '',
				]];
				break;
		}

		if (!$params) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Params is empty";
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/telegram/_requestForm', ['method' => $method, 'params' => $params]);
		die (json_encode($this->ajaxResponse));
	}

	public function telegramRequest()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$token = trim($this->input()->post('token', true));
		$method = trim($this->input()->post('method', true));

		$params = [];
		parse_str($this->input()->post('params'), $params);

		$r = $this->notices()->telegram()->request($method, $params, $token);

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/telegram/_requestResult', [
			'request'	=> '<pre>' . $r->getInfo() . '</pre>',
			'response'	=> !$r->success() ? $r->getError() : '<pre>' . print_r(json_decode($r->getMessage(), 1), 1) . '</pre>'
		]);
		die (json_encode($this->ajaxResponse));
	}

	public function editTelegramTemplate()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$template = $this->notices()->telegram()->templates()->get(intval($this->input()->post('tid', true)));
		if (!$template) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/telegram/_editTemplate', [
			'template'	=> $template,
			'languages'	=> $this->languages()
		]);
		die (json_encode($this->ajaxResponse));
	}

	public function saveTelegramTemplate()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$template = $this->notices()->telegram()->templates()->get(intval($this->input()->post('tid', true)));
		if (!$template) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Объект не найден";
			die (json_encode($this->ajaxResponse));
		}

		$params = [];
		parse_str($this->input()->post('params'), $params);

		$generalData = $params['field'];

		if (!$generalData['description']) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Не указано описание";
			die (json_encode($this->ajaxResponse));
		}

		Db::begin();

		// DATA
		$data = $params['data'] ?: [];
		foreach ($data as $lang => $d) {
			$d = array_map('trim', $d);
			$exist = $this->notices()->telegram()->templates()->getData(new Filter(['TemplateId' => $template->getId(), 'Language' => $lang]));
			$u = $this->notices()->telegram()->templates()->_newData()->setMessage($d['Message'])->setLanguage($lang)->setTemplateId($template->getId());
			if (!$exist && !$this->notices()->telegram()->templates()->createData($u)) {
				Db::rollback();
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = "Ошибка создания данных";
				die (json_encode($this->ajaxResponse));
			} elseif ($exist && !$this->notices()->telegram()->templates()->updateData($template->getId(), $lang, $u->toArray())) {
				Db::rollback();
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = "Ошибка обновления данных";
				die (json_encode($this->ajaxResponse));
			}
		}

		//обновляем запись
		if (!$this->notices()->telegram()->templates()->update($template->getId(), $generalData)) {
			Db::rollback();
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Ошибка обновления";
			die (json_encode($this->ajaxResponse));
		}

		Db::commit();

		$this->ajaxResponse['descr'] = "Изменения успешно сохранены";
		die (json_encode($this->ajaxResponse));
	}

	public function createTelegramTemplate()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$label = strtolower($this->input()->post('ident', true));

		if ($this->notices()->telegram()->templates()->getByLabel($label)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Шаблон [ '.$label.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($label, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$t = $this->notices()->telegram()->templates()->_new()->setLabel($label);
		if (!$this->notices()->telegram()->templates()->create($t)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die (json_encode($this->ajaxResponse));
		}

		$this->_notify('Шаблон успешно создан');

		die (json_encode($this->ajaxResponse));
	}

	public function deleteTelegramTemplate()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$template = $this->notices()->telegram()->templates()->get(intval($this->input()->post('id', true)));
		if (!$template) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Объект не найден';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на использование в уведомлениях
		if ($template->getIsUsed()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Шаблон используется в уведомлениях';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на использование аккаунта в уведомлениях
		if ($template->getRequired()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить этот шаблон';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->telegram()->templates()->delete($template->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}

		$this->ajaxResponse['descr'] = 'Шаблон удалено успешн';
		die (json_encode($this->ajaxResponse));
	}
// -END Telegram

// Events
	public function events($eventId = false)
	{
		// Список всех уведомлений
		$events = $this->notices()->events()->gets(new Filter());

		// Список всех уведомлений, разбитый по группам
		$groups = $this->notices()->events()->groups()->gets(new Filter());
		foreach($groups as $g) {
			foreach($events as $e) {
				if ($g->getId() == $e->getGroupId()) $g->addEvent($e);
			}
		}
		$this->vars['groups'] = $groups;

		// Если передан ID уведомления, то берем его параметры
		$event = $eventId ? ($this->notices()->events()->get($eventId) ?: $this->notices()->events()->getByIdent($eventId)) : false;
		$this->vars['event'] = $event;

		//Проверка на корректность настроек
		$this->vars['isValid'] = $event ? $this->notices()->events()->isValid($event) : false;

		$this->vars['letters'] = $this->notices()->email()->letters()->gets(new Filter());
		$this->vars['accounts'] = $this->notices()->email()->accounts()->gets(new Filter());
		$this->vars['templates'] = $this->notices()->telegram()->templates()->gets(new Filter());

		//echo '<pre>',print_r($this->vars['templates'], 1),'</pre>'; die();

		$this->vars['defaultAccount'] = ($defaultAccount = $this->notices()->email()->accounts()->getDefault()) ? ' ('.$defaultAccount->getEmail().') ' : '';

		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');

		$this->vars['currentMenu'] = 'events';
		$this->layout()->page()->setHeader('Настройка уведомлений. События');
		$this->tpl()->template($this->module.'/events/index');
	}

	public function toggleEvent()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		$event = $this->notices()->events()->get($id);
		if (!$event) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Событие не найдено';
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($value, [0, 1])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное значение данных: ' . $value;
			die (json_encode($this->ajaxResponse));
		}

		if (!in_array($field, ['UserUseEmail', 'UserUseSms', 'UserUseTelegram', 'AdminUseEmail', 'AdminUseSms', 'AdminUseTelegram'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное поле: ' . $field;
			die (json_encode($this->ajaxResponse));
		}

		$d = $this->notices()->events()->_newData()->setEventId($event->getId())->{'set' . $field}($value);
		$exist = $this->notices()->events()->getData($d->getEventId());
		if (!$exist && !$this->notices()->events()->createData($d)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания данных';
			die (json_encode($this->ajaxResponse));
		} elseif($exist && !$this->notices()->events()->updateData($d->getEventId(), $d->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления данных';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на корректность настроек
		$isValid = $this->notices()->events()->isValid($this->notices()->events()->get($d->getEventId()));
		$this->ajaxResponse['validStatus'] = $isValid->success();
		$this->ajaxResponse['validText'] = $isValid->success() ? $isValid->getMessage() : $isValid->getError();

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function updateEvent()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$value = trim($this->input()->post('value', true));

		$event = $this->notices()->events()->get($id);
		if (!$event) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Событие не найдено';
			die (json_encode($this->ajaxResponse));
		}

		$allowFields = [
			'GroupId', 'description', 'Comment',
			'UserLetterId', 'UserAccountId', 'UserSmsTemplateId',
			'UserTelegramTemplateId', 'AdminLetterId', 'AdminAccountId',
			'AdminSmsTemplateId', 'AdminTelegramTemplateId', 'AdminEmailRecipient',
			'AdminSmsRecipient', 'AdminTelegramRecipient'];
		if (!in_array($field, $allowFields)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверное поле: ' . $field;
			die (json_encode($this->ajaxResponse));
		}

		if (in_array($field, ['GroupId', 'description', 'Comment'])) {
			$e = $this->notices()->events()->_new()->{'set' . $field}($value);
			if(!$this->notices()->events()->update($event->getId(), $e->toArray())) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка обновления данных';
				die (json_encode($this->ajaxResponse));
			}
		} else {
			$d = $this->notices()->events()->_newData()->setEventId($event->getId())->{'set' . $field}($value);
			$exist = $this->notices()->events()->getData($d->getEventId());
			if (!$exist && !$this->notices()->events()->createData($d)) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка создания данных';
				die (json_encode($this->ajaxResponse));
			} elseif($exist && !$this->notices()->events()->updateData($d->getEventId(), $d->toArray())) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка обновления данных';
				die (json_encode($this->ajaxResponse));
			}
		}

		// Проверка на корректность настроек
		$isValid = $this->notices()->events()->isValid($this->notices()->events()->get($event->getId()));
		$this->ajaxResponse['validStatus'] = $isValid->success();
		$this->ajaxResponse['validText'] = $isValid->success() ? $isValid->getMessage() : $isValid->getError();

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function createEvent()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = strtolower($this->input()->post('ident', true));

		if ($this->notices()->events()->getByIdent($ident)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Событие с идентификатором [ '.$ident.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($ident, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$e = $this->notices()->events()->_new()
			->setGroupId(1) // Разное
			->setIdent($ident);

		if ($this->notices()->events()->create($e))
			$this->ajaxResponse['redirect'] = '/' . ADMIN . '/' . $this->module . '/events/' . $e->getId();
		else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания События';
		}

		die (json_encode($this->ajaxResponse));
	}

	public function createEventGroup()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$g = $this->notices()->events()->groups()->_new()->setName($this->input()->post('name', true));

		if ($this->notices()->events()->groups()->create($g))
			$this->ajaxResponse['descr'] = 'Группа успешно создания';
		else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания группы';
		}

		die (json_encode($this->ajaxResponse));
	}
// -END Events

// Email
	public function emails()
	{
		$accounts = $this->notices()->email()->accounts()->gets(new Filter());
		$this->vars['accounts'] = $accounts;

		$lg = 12;
		if($accounts){
			if(count($accounts) == 1) $lg = 12;
			else if(count($accounts) == 2) $lg = 6;
			else if(count($accounts) == 3) $lg = 4;
			else $lg = 3;
		}
		$this->vars['lg'] = $lg;

		$this->addCss[] = '/admin/css/modules/notices.css'.Func::modifyTime('/admin/css/modules/notices.css');
		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');

		$this->vars['currentMenu'] = 'emails';
		$this->layout()->page()->setHeader('Настройка E-mail аккаунтов');
		$this->tpl()->template($this->module.'/emails/index');
	}

	public function createEmail()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = strtolower($this->input()->post('ident', true));

		if ($this->notices()->email()->accounts()->getByIdent($ident)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт с Идентификатором [ '.$ident.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($ident, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		$a = $this->notices()->email()->accounts()->_new()
			->setIdent($ident)
			->setEmail('email@domain.com')
			->setName('Имя Отправителя')
			->setUser('email@domain.com')
			->setPassword('YourPassWord')
			->setHost('ssl://smtp.yandex.ru')
			->setPort(465);

		$res = $this->notices()->email()->accounts()->create($a);

		if (!$res->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания Email-аккаунта';
		}

		$this->_notify('Аккаунт успешно создан');

		die (json_encode($this->ajaxResponse));
	}

	public function deleteEmail()
	{
		//Проверяем корректность запроса.
		if (!$this->input()->isAjax()) die();

		$account = $this->notices()->email()->accounts()->get(intval($this->input()->post('id', true)));
		if (!$account) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт не найден';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на использование аккаунта в уведомлениях
		if ($account->getUsed()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт используется в уведомлениях';
			die (json_encode($this->ajaxResponse));
		}

		if ($account->getIsDefault()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Нельзя удалять аккаунт по умолчанию';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->email()->accounts()->delete($account->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления Email-аккаунта';
		}

		$this->_notify('Аккаунт удален успешно');
		die (json_encode($this->ajaxResponse));
	}

	public function setDefaultEmail()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$account = $this->notices()->email()->accounts()->get(intval($this->input()->post('id', true)));
		if (!$account) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->email()->accounts()->setDefault($account->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
		}

		$this->_notify("Аккаунт {$account->getIdent()} установлен как аккаунт по умолчанию");

		die (json_encode($this->ajaxResponse));
	}

	public function checkEmail()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$account = $this->notices()->email()->accounts()->get(intval($this->input()->post('id', true)));
		if (!$account) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт не найден';
			die (json_encode($this->ajaxResponse));
		}
		$toEmail = trim($this->input()->post('email', true));

		//Отправляем Email
		$email = new Email();
		$email->initialize($account->getParams());
		$email->from($account->getEmail(), $account->getName());
		$email->to($toEmail);
		$email->subject('testing');
		$email->message('testing');

		$this->ajaxResponse['descr'] = $email->send() ? "Successfuly \r\r" : "Error \r\r";
		$this->ajaxResponse['descr'] .= $email->print_debugger();

		die (json_encode($this->ajaxResponse));
	}

	//Сохранение параметров Email аккаунта
	public function updateEmail()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$account = $this->notices()->email()->accounts()->get(intval($this->input()->post('id', true)));
		if (!$account) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Аккаунт не найден';
			die (json_encode($this->ajaxResponse));
		}

		$email = strtolower(trim($this->input()->post('email', true)));
		$name = $this->input()->post('name', true);
		$host = $this->input()->post('host', true);
		$port = $this->input()->post('port', true);
		$user = $this->input()->post('user', true);
		$pass = $this->input()->post('pass', true);

		if (!preg_match("/.+@.+\..+/i", $email)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверный формат записи E-mail';
			die (json_encode($this->ajaxResponse));
		}

		if ($port && !preg_match("/^[0-9]+$/i", $port)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'SMTP порт должен быть числом';
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->notices()->email()->accounts()->_new()
			->setEmail($email)
			->setName($name)
			->setUser($user)
			->setHost($host)
			->setPort($port)
			->setPassword($this->encrypt()->enc($pass));

		//Обновление
		if (!$this->notices()->email()->accounts()->update($account->getId(), $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка записи в БД';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Данные сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}
// -END Email

// Letters
	public function letters()
	{
		$this->vars['letters'] = $this->notices()->email()->letters()->gets(new Filter());

		$this->addCss[] = '/admin/css/modules/notices.css'.Func::modifyTime('/admin/css/modules/notices.css');
		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');

		// All languages
		$this->vars['languages'] = $this->languages();

		$this->layout()->page()->setHeader('Шаблоны писем');
		$this->tpl()->template($this->module.'/letters/index');
	}

	public function createLetter()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ident = strtolower(trim($this->input()->post('ident', true)));

		if (!$ident) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не указан идентификатор';
			die (json_encode($this->ajaxResponse));
		}

		if (false != ($wrongSimbols = \Helper\Text::wrongSimbols($ident, 'a-z0-9_'))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Недопустимые символы: ' . implode(' ', $wrongSimbols);
			die (json_encode($this->ajaxResponse));
		}

		if ($this->notices()->email()->letters()->getByIdent($ident)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Шаблон с идентификатором [ '.$ident.' ] уже есть в системе';
			die (json_encode($this->ajaxResponse));
		}

		$l = $this->notices()->email()->letters()->_new()->setIdent($ident);

		if ($this->notices()->email()->letters()->create($l)) {
			$this->ajaxResponse['redirect'] = '/' . ADMIN . '/' . $this->module . '/editLetter/' . $l->getId();
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания Шаблона';
		}

		die (json_encode($this->ajaxResponse));
	}

	public function deleteLetter()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));

		if (false == ($item = $this->notices()->email()->letters()->get($id))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}


		if ($item->getIsSystem()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Невозможно удалить системный шаблон';
			die (json_encode($this->ajaxResponse));
		}

		// Проверка на использование в уведомлениях
		if ($item->getIsUsed()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Шаблон используется в уведомлениях';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->notices()->email()->letters()->delete($item)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления шаблона';
		}

		$this->ajaxResponse['descr'] = 'Шаблон удален успешно';
		die (json_encode($this->ajaxResponse));
	}

	// Редактирование
	public function editLetter($id = false)
	{
		$letter = $this->notices()->email()->letters()->get($id);
		if(!$letter) $this->show404();

		// Result
		$result = $this->_save($letter);
		if ($result->success()) $letter = $this->notices()->email()->letters()->get($letter->getId());
		$this->vars['result'] = $result;

		// All languages
		$this->vars['languages'] = $this->languages();

		$this->vars['letter'] = $letter;

		$this->addJs[] = '/admin/js/modules/notices.js'.Func::modifyTime('/admin/js/modules/notices.js');
		//$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		//$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		//$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		$this->addCss[] = '/admin/css/modules/notices.css'.Func::modifyTime('/admin/css/modules/notices.css');

		$this->layout()->page()->setHeader('Шаблон: ' . $letter->getIdent());
		$this->tpl()->template($this->module.'/letters/edit');
	}

	/**
	 * @param Letter
	 * @return Result
	 */
	public function _save(Letter $letter)
	{
		// Result
		$result = new Result();

		if($this->input()->post('save'))
		{
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['description']))
					unset($fields[$field]);

			if (!$fields['description'])
				$result->setError($result->getError() . 'Не указано описание' . '<br />');

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			// DATA
			$data = $this->input()->post('data') ?: [];
			foreach ($data as $lang => $d) {
				$d = array_map('trim', $d);

				$exist = $this->notices()->email()->letters()->getData(new Filter(['LetterId' => $letter->getId(), 'Language' => $lang]));
				$u = $this->notices()->email()->letters()->_newData()
					->setSubject($d['Subject'])
					->setBody($d['Body'])
					->setLetterId($letter->getId())
					->setLanguage($lang);
				if(!$exist && !$this->notices()->email()->letters()->createData($u)){
					return $result->setError('Ошибка создания данных');
				}elseif($exist && !$this->notices()->email()->letters()->updateData($letter->getId(), $lang, $u->toArray())){
					return $result->setError('Ошибка обновления данных');
				}
			}

			//обновляем запись
			if($this->notices()->email()->letters()->update($letter->getId(), $fields)){
				$result->setStatus(true);
				$result->setMessage('Изменения успешно сохранены');
			}else{
				$result->setError('Ошибка обновления');
			}
		}

		return $result;
	}

	public function showLetterModal()
	{
		// Проверяем корректность запроса...
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get($this->module.'/letters/_show', ['lid' => intval($this->input()->post('lid', true)), 'lang' => strval($this->input()->post('lang', true))]);
		die (json_encode($this->ajaxResponse));
	}

	public function showLetter()
	{
		if (false == ($letter = $this->notices()->email()->letters()->get(intval($this->input()->get('lid', true))))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}
		$lang = strval($this->input()->get('lang', true));
		echo print_r($letter->getData($lang)->getBody(), 1); die();
	}
// -END Letters
}