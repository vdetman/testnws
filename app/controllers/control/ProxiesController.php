<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use Entity\DateTime;
use Proxies\Entity\Proxy;

class ProxiesController extends AbstractControlController
{
	protected $module = 'proxies';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'proxies';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'Page'		=> $this->input()->get('page') ?: 1,
			'PerPage'	=> ADMIN_PERPAGE,
			'order_by'	=> 'ProxyId',
			'order_dir'	=> 'ASC',
		]);

		// Order
		if (null !== ($order = $this->input()->get('Order', true))) {
			$filter->set('Order', $order);
			switch($order){
				case 'id_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'ProxyId'); break;
				case 'id_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'ProxyId'); break;
				case 'ip_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'ip'); break;
				case 'ip_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'ip'); break;
				case 'ex_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'expires_at'); break;
				case 'ex_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'expires_at'); break;
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

		// Group
		if (!is_null($value = $this->input()->get('Group'))) {
			switch($value){
				case 'ch': $filter->set('Group', $value); break;
				case 'php': $filter->set('Group', $value); break;
			}
		}

		// Search
		if ($s = trim($this->input()->get('search')))
			$filter->set('search', $s);

		$this->vars['proxies'] = $this->proxies()->gets($filter);

		$filter->setTotal($this->proxies()->getTotal());

		$this->vars['filter'] = $filter;

		// Отправляем GET параметры в шаблон
		$this->vars['query'] = json_encode($this->input()->get(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		//Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('Page'),
			'perPage'	=> $filter->get('PerPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		//Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addCss[] = '/admin/css/modules/proxies.css'.Func::modifyTime('/admin/css/modules/proxies.css');
		$this->addJs[] = '/admin/js/modules/proxies.js'.Func::modifyTime('/admin/js/modules/proxies.js');

		$this->layout()->page()->setHeader('Список Proxy');
		$this->tpl()->template($this->module.'/index');
	}

	public function edit($id = false)
	{
		if(false == ($proxy = $this->proxies()->get($id))) $this->show404();
		$this->_save($proxy); // Saving
		$this->vars['proxy'] = $proxy;
		$this->addJs[] = '/admin/js/modules/proxies.js'.Func::modifyTime('/admin/js/modules/proxies.js');
		$this->layout()->page()->setHeader($proxy->getIp() . ':' . $proxy->getPort());
		$this->tpl()->template($this->module.'/edit');
	}

	/**
	 * @param Proxy
	 * @return boolean
	 */
	public function _save(Proxy $proxy)
	{
		if ($this->input()->post('save')) {

			$errors = [];

			// POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			// Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['ip','Port','name','Login','password','expires_at','status']))
					unset($fields[$field]);

			if (!$this->input()->isValidIp($fields['ip'])) $errors[] = 'Не указан адрес';
			if (!intval($fields['Port'])) $errors[] = 'Не указан порт';
			if (!$fields['name']) $errors[] = 'Не указано название';
			if ($fields['expires_at'] && !preg_match('~^[\d]{2}\.[\d]{2}\.[\d]{4}$~i', $fields['expires_at'])) $errors[] = 'Некорректная дата';

			$this->vars['post'] = $fields;

			// Если насобирали ошибки, то выход..
			if ($errors) {
				$this->_notify(implode('<br/>', $errors), false);
				return false;
			}

			$p = $this->proxies()->_new()
				->setIp($fields['ip'])
				->setPort(intval($fields['Port']))
				->setName($fields['name'])
				->setLogin($fields['Login'])
				->setPassword($fields['password'])
				->setStatus($fields['status'])
				->setExpires(new DateTime(date('Y-m-d', strtotime($fields['expires_at']))));

			//обновляем запись
			if (!$this->proxies()->update($proxy->getId(), $p->toArray())) {
				$this->_notify('Ошибка обновления', false);
				return false;
			}

			$this->_notify('Изменения успешно сохранены');
			$proxy->fromArray($p->toArray());
		}
	}

	public function create()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$ip = trim($this->input()->post('ip', true));

		if (!$this->input()->isValidIp($ip)){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Некорректный адрес';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->proxies()->_new()->setIp($ip)->setStatus('disabled');
		if (!$this->proxies()->create($n)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка создания';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['redirect'] = '/' . ADMIN . '/' . $this->module . '/edit/' . $n->getId();

		die (json_encode($this->ajaxResponse));
	}

	public function delete()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$proxy = $this->proxies()->get(intval($this->input()->post('pid', true)));
		if (!$proxy) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->proxies()->delete($proxy->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Proxy [' . $proxy->getIp() . '] успешно удален';

		die (json_encode($this->ajaxResponse));
	}

	public function toggle()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));
		$field = trim($this->input()->post('field', true));
		$value = trim($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Недопустимое поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		if(!in_array($value, [0, 1])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное значение {$field}: {$value}";
			die (json_encode($this->ajaxResponse));
		}

		$proxy = $this->proxies()->get($id);
		if (!$proxy) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$p = $this->proxies()->_new();
		switch ($field) {
			case 'status':
				$p->setStatus($value == 1 ? 'active' : 'disabled');
				break;
		}

		// Обновление
		if (!$this->proxies()->update($proxy->getId(), $p->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function check()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$proxy = $this->proxies()->get(intval($this->input()->post('pid', true)));
		if (!$proxy) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$s = microtime(1);

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, 'https://ya.ru');
		curl_setopt($c, CURLOPT_FAILONERROR, false);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_TIMEOUT, 3);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0");
		curl_setopt($c, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt($c, CURLOPT_PROXY, $proxy->getIp());
		curl_setopt($c, CURLOPT_PROXYPORT, $proxy->getPort());
		curl_setopt($c, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
		curl_setopt($c, CURLOPT_PROXYUSERPWD, $proxy->getLogin() . ':' . $proxy->getPassword());
		curl_exec($c);
		$responseCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
		$this->ajaxResponse['status'] = in_array($responseCode, [200,302]);
		$this->ajaxResponse['descr'] = (200 == $responseCode ? "Successfuly; Time: " : "Failure; Time: ") . round(microtime(1) - $s, 4) . ' sec';
		die (json_encode($this->ajaxResponse));
	}

	public function exportList()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$this->_clean_tmp_files();

		$queryStr = trim($this->input()->post('query', true));
		$query = json_decode($queryStr, true);

		// Фильтрация
		$filter = new Filter(['order_by'	=> 'ProxyId', 'order_dir'	=> 'ASC',]);

		// Order
		if (!empty($query['Order'])) {
			$filter->set('Order', $query['Order']);
			switch($query['Order']){
				case 'id_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'ProxyId'); break;
				case 'id_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'ProxyId'); break;
				case 'ip_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'ip'); break;
				case 'ip_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'ip'); break;
				case 'ex_asc':	$filter->set('order_dir', 'ASC');	$filter->set('order_by', 'expires_at'); break;
				case 'ex_desc':	$filter->set('order_dir', 'DESC');	$filter->set('order_by', 'expires_at'); break;
			}
		}

		// Status
		if (!empty($query['status']) && 'all' != $query['status'])
			$filter->set('status', $query['status']);

		// Search
		if (!empty($query['search']))
			$filter->set('search', $query['search']);

		$result = $this->_exportList($filter);

		// Формируем файл
		if (!$result->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $result->getError();
			die (json_encode($this->ajaxResponse));
		}

		$file = $result->getObject();

		// Формируем токен для доступа к файлу
		$dl = new Download();
		if (!$dl->setFile($file['path'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $dl->getError();
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['redirect'] = '/' . ADMIN . '/download';

		die (json_encode($this->ajaxResponse));
	}

	/**
	 * @param Filter
	 * @return Result
	 */
	private function _exportList(Filter $filter)
	{
		$result = new Result();

		$txt = "";
		foreach ($proxies = $this->proxies()->gets($filter) as $p)
			$txt .= $p->getIp() . "\n";

		// Имя файла
		$fileName = 'Proxy_IPs.txt';

		// Итоговый путь к файлу
		$filePath = VF_TMP_DIR . '/' . $fileName;

		$f = fopen($filePath, 'w');
		fwrite($f, $txt);
		fclose($f);

		if (!is_file($filePath))
			return $result->setError('File not found');

		return $result->setStatus(true)->setObject(['name' => $fileName, 'path' => $filePath]);
	}

	public function export()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$this->_clean_tmp_files();

		$format = trim(strtolower($this->input()->post('format', true)));
		$format = in_array($format, ['sql','xls']) ? $format : 'xls';

		switch ($format) {
			case 'sql': $result = $this->_exportSQL(); break;
			case 'xls': $result = $this->_exportXLS(); break;
		}

		// Формируем файл
		if (!$result->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $result->getError();
			die (json_encode($this->ajaxResponse));
		}

		$file = $result->getObject();

		// Формируем токен для доступа к файлу
		$dl = new Download();
		if (!$dl->setFile($file['path'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $dl->getError();
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['redirect'] = '/' . ADMIN . '/download';

		die (json_encode($this->ajaxResponse));
	}

	/**
	 * @return Result
	 */
	private function _exportSQL()
	{
		$result = new Result();

		// Список прокси
		$proxies = $this->proxies()->gets(new Filter());
		if (!$proxies) return $result->setError('Elements not found');

		// Данные таблицы БД
		$table = ProxiesModel::$table;
		$columns = Db::getTableColumns($table);
		if (!$columns) return $result->setError('Cant get columns');

		// Проверка наличия обязательных полей
		$absentRequiredFileds = array_diff(['ProxyId','Port','name','ip','Login','password','expires_at','status'], $columns);
		if ($absentRequiredFileds) return $result->setError("Required columns not found in table `{$table}`: " . implode(", ", $absentRequiredFileds));

		$sql = "INSERT IGNORE INTO `{$table}` (`Port`,`Name`,`Ip`,`Login`,`Password`,`Expires`,`Status`) VALUES \n";
		$values = [];
		foreach ($proxies as $p) {
			$values[] = "(" . implode(", ", [
				$p->getPort(),
				"'{$p->getName()}'",
				"'{$p->getIp()}'",
				"'{$p->getLogin()}'",
				"'{$p->getPassword()}'",
				$p->getExpires() ? "'{$p->getExpires()->format('Y-m-d')}'" : 'NULL',
				"'{$p->getStatus()}'",
			]) . ")";
		}
		$sql .= implode(",\n", $values) . ";";

		// Имя файла
		$fileName = 'Proxies_' . date('Ymd_His') . '.sql';

		// Итоговый путь к файлу
		$filePath = VF_TMP_DIR . '/' . $fileName;

		$f = fopen($filePath, 'w');
		fwrite($f, $sql);
		fclose($f);

		if (!is_file($filePath))
			return $result->setError('File not found');

		return $result->setStatus(true)->setObject(['name' => $fileName, 'path' => $filePath]);
	}

	/**
	 * @return Result
	 */
	private function _exportXLS()
	{
		$result = new Result();

		// Список прокси
		$proxies = $this->proxies()->gets(new Filter());
		if (!$proxies) return $result->setError('Elements not found');

		$excel = $this->load()->library('excel');
		$excel->setActiveSheetIndex(0); // Устанавливаем индекс активного листа
		$sheet = $excel->getActiveSheet(); // Получаем активный лист
		$sheet->setTitle('Sheet'); // Подписываем лист

		// Общие параметры шапки
		$head = [
			0	=> ['n' => 'ip', 'w' => 16],
			1	=> ['n' => 'Port', 'w' => 7],
			2	=> ['n' => 'Login', 'w' => 11],
			3	=> ['n' => 'password', 'w' => 11],
			4	=> ['n' => 'expires_at', 'w' => 11],
			5	=> ['n' => 'status', 'w' => 8],
			6	=> ['n' => 'name', 'w' => 50],
		];

		// Шапка
		foreach ($head as $c => $d) {
			$sheet->setCellValueByColumnAndRow($c, 1, $d['n']);
			$sheet->getStyleByColumnAndRow($c, 1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$sheet->getStyleByColumnAndRow($c, 1)->getFill()->getStartColor()->setRGB('AC12D9');
			$sheet->getStyleByColumnAndRow($c, 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyleByColumnAndRow($c, 1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$sheet->getStyleByColumnAndRow($c, 1)->getAlignment()->setWrapText(true);
			$sheet->getColumnDimensionByColumn($c)->setWidth($d['w']);

			// Font
			$sheet->getStyleByColumnAndRow($c, 1)->getFont()->setBold(true);
			$sheet->getStyleByColumnAndRow($c, 1)->getFont()->getColor()->applyFromArray(['rgb' => 'FFFFFF']);
		}

		$row = 2;

		foreach ($proxies as $p) {
			$sheet->setCellValueByColumnAndRow(0, $row, $p->getIp());
			$sheet->setCellValueByColumnAndRow(1, $row, $p->getPort());
			$sheet->setCellValueByColumnAndRow(2, $row, $p->getLogin());
			$sheet->setCellValueByColumnAndRow(3, $row, $p->getPassword());
			$sheet->setCellValueByColumnAndRow(4, $row, $p->getExpires() ? $p->getExpires()->format('d.m.Y') : '');
			$sheet->setCellValueByColumnAndRow(5, $row, $p->getStatus());
			$sheet->setCellValueByColumnAndRow(6, $row, $p->getName());
			$row++;
		}

		// Все тонкие границы
		$sheet->getStyleByColumnAndRow(0, 1, count($head) - 1, count($proxies) + 1)->applyFromArray([
			'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN,'color' => ['rgb' => '000000']]]
		]);

		// Жирная шапка
		$sheet->getStyleByColumnAndRow(0, 1, count($head) - 1, 1)->applyFromArray([
			'borders' => ['outline' => ['style' => PHPExcel_Style_Border::BORDER_THICK,'color' => ['rgb' => '000000']]]
		]);

		// Имя файла
		$fileName = 'Proxies_' . date('Ymd_His') . '.xlsx';

		// Итоговый путь к файлу
		$filePath = VF_TMP_DIR . '/' . $fileName;

		if (!$excel->save($filePath))
			return $result->setError('Cant save file');

		return $result->setStatus(true)->setObject(['name' => $fileName, 'path' => $filePath]);
	}
// -END Export

// Import
	public function import()
	{
		$isUploadedFile = $this->_isUploadedFile();
		$this->vars['result'] = $isUploadedFile;

		$this->vars['processImport'] = $this->_processImport();

		if ($isUploadedFile->success()) {

			// Переданный набор полей
			$iFieldsSet = [];

			// Импортируемые данные
			$iProxies = [];
			foreach($isUploadedFile->getObject() as $p) {
				if (!$iFieldsSet) $iFieldsSet = array_keys($p);
				$iProxies[trim($p['ip']) . '~' . trim($p['Port'])] = $p;
			}

			// Отфильтруем полученные поля
			$iFieldsSet = array_intersect($iFieldsSet, ['Port','name','ip','Login','password','expires_at','status']);

			// Существующие данные
			$eProxies = [];
			foreach($this->proxies()->gets(new Filter([])) as $p)
				$eProxies[trim($p->getIp()) . '~' . trim($p->getPort())] = $p;

			$DATA = [];
			$DATA[] = $iFieldsSet; // HEAD

			foreach ($iProxies as $key => $iProxy) {
				$row = [];
				foreach ($iFieldsSet as $field) {

					// Определяем существующее значение
					$oldValue = '';
					if (!empty($eProxies[$key])) {
						switch ($field) {
							case 'expires_at': $oldValue = $eProxies[$key]->getExpires() ? $eProxies[$key]->getExpires()->format('d.m.Y') : ''; break;
							default: $oldValue = $eProxies[$key]->{'get' . $field}(); break;
						}
					}

					$cell = [
						'field'		=> $field,
						'value'		=> isset($iProxy[$field]) ? $iProxy[$field] : '',
						'oldValue'	=> $oldValue,
						'exist'		=> isset($eProxies[$key])
					];
					$cell['update'] = $cell['value'] != $cell['oldValue'];
					$row[] = $cell;
				}
				$DATA[$key] = $row;
			}
			ksort($DATA);
			$this->vars['DATA'] = $DATA;
		}

		$this->addJs[] = '/admin/js/modules/proxies.js'.Func::modifyTime('/admin/js/modules/proxies.js');
		$this->addCss[] = '/admin/css/modules/proxies.css'.Func::modifyTime('/admin/css/modules/proxies.css');

		$this->layout()->page()->setHeader('Proxy. Импорт');
		$this->tpl()->template($this->module.'/import');
	}

	/**
	 * @return Result
	 */
	private function _isUploadedFile()
	{
		$result = new Result();

		// Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path'	=> VF_TMP_DIR,
			'allowed_types'	=> 'xls|xlsx',
			'max_size'		=> '10120',
			'overwrite'		=> true,
		]);

		if ($upl->do_upload('file')) {
			$upInfo = $upl->data();
			switch ($upInfo['file_type']) {
				case 'application/vnd.ms-excel':
				case 'application/vnd.ms-office':
				case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
					return $this->_parseXLS($upInfo);
				default: return $result->setError('Wrong file type');
			}
		} elseif ($upl->display_errors()) {
			return $result->setError($upl->display_errors('',''));
		}

		return $result;
	}

	/**
	 * @param array
	 * @return Result
	 */
	private function _parseXLS($upInfo)
	{
		$result = new Result();

		$this->load()->library('excel');
		$excel = new Excel();
		$xls = $excel->load($upInfo['full_path']);
		$sheet = $xls->getSheet(0);

		$numRows = $sheet->getHighestRow();
		$cols = $sheet->getColumnDimensions();

		// Определяем МЭП column->lang
		$c = 0;
		$fieldsMap = [];
		foreach (array_keys($cols) as $letter)
			$fieldsMap[$letter] = ucfirst(trim(strtolower($sheet->getCellByColumnAndRow($c++, 1)->getValue())));

		$proxies = [];
		for ($row = 2; $row <= $numRows; $row++) {
			foreach (array_keys($cols) as $letter) {
				$cell = $sheet->getCell($letter . $row);
				$proxies[$row][$fieldsMap[$letter]] = $cell ? trim($cell->getValue()) : '';
			}
		}

		return $result->setObject($proxies)->setStatus(true);
	}

	/**
	 * Сохранение
	 * @return Result
	 */
	private function _processImport()
	{
		// Result
		$result = new Result();

		if($this->input()->post('proxy'))
		{
			//POST
			$proxies = $this->input()->post('proxy') ?: [];
			$comfirms = $this->input()->post('comfirm') ?: [];

			$filteredProxies = array_intersect_key ($proxies, $comfirms);
			if (!$filteredProxies) return $result->setError('Нет данных для импорта');

			// Существующие данные
			$eProxies = [];
			foreach($this->proxies()->gets(new Filter([])) as $p)
				$eProxies[trim($p->getIp()) . '~' . trim($p->getPort())] = $p;

			$this->db()->begin();
			foreach($filteredProxies as $key => $data) {
				$p = $this->proxies()->_new();
				foreach($data as $field => $value) {
					switch ($field) {
						case 'ip': $p->setIp($value); break;
						case 'Port': $p->setPort($value); break;
						case 'Login': $p->setLogin($value); break;
						case 'password': $p->setPassword($value); break;
						case 'name': $p->setName($value); break;
						case 'status': $p->setStatus($value); break;
						case 'expires_at': $p->setExpires(new DateTime($value)); break;
					}
				}
				$exist = isset($eProxies[$key]) ? $eProxies[$key] : false;
				if(!$exist && !$this->proxies()->create($p)){
					$this->db()->rollback();
					return $result->setError('Ошибка создания данных');
				}elseif($exist && !$this->proxies()->update($exist->getId(), $p->toArray())){
					$this->db()->rollback();
					return $result->setError('Ошибка обновления данных');
				}
			}
			$this->db()->commit();

			$result
				->setStatus(true)
				->setMessage('Импорт успешно проведен');
		}

		return $result;
	}
// -END Labels
}