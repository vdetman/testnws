<?php if (!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;

class NewsController extends AbstractController
{
	const LIMIT		= 20;
	const ADD_MORE	= 15;

	public function __construct()
	{
		parent::__construct();
		$this->vars['current'] = 'news';
		$this->vars['currentRubric'] = 0;
	}

	public function index()
	{
		// Получаем GET параметры
		$get = $this->input()->get();

		// Параметры фильтрации
		$filter = new Filter(array_merge($this->_getDefaultFilter($get), ['limit' => self::LIMIT]));
		$newsList = $this->news()->gets($filter);
		$this->vars['newsList'] = $newsList;
		//echo '<pre>',print_r($this->vars['newsList'], 1),'</pre>'; die();

		// For get filters & ajax load more
		$this->vars['loaded'] = count($newsList);
		$this->vars['total'] = $this->news()->getTotal();
		$this->vars['filter'] = json_encode($get, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

		// Дерево рубрик
		$this->vars['rubricTree'] = $this->news()->getTree();
		//echo '<pre>',print_r($this->vars['rubricTree'], 1),'</pre>'; die();

		$this->addJs[] = Func::withModifyTime('/js/news.js');
		$this->layout()->page()->setTitle('Список новостей')->setHeader('Список новостей');
		$this->tpl()->template('news/index');
	}

	public function loadMore()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		// Едиственный шанс увидеть лоадер ;-)
		usleep(700000);

		$loaded = intval($this->input()->post('loaded', true));
		$get = json_decode(trim($this->input()->post('filter', true)), true);

		// Список
		$filter = new Filter(array_merge($this->_getDefaultFilter($get), ['limit' => self::ADD_MORE, 'offset' => $loaded]));
		$newsList = $this->news()->gets($filter);

		$elements = '';
		foreach ($newsList as $news)
			$elements .= $this->tpl()->get('news/_item', ['news' => $news]);
		$this->ajaxResponse['elements'] = $elements;

		$loaded = $loaded + count($newsList);
		$this->ajaxResponse['loaded'] = $loaded;

		$total = $this->news()->getTotal();
		$this->ajaxResponse['total'] = $total;

		$this->ajaxResponse['isFinish'] = $total <= $loaded;

		die(json_encode($this->ajaxResponse));
	}

	/**
	 * Формирования массива фильтрации новостей
	 * @param array
	 * @return array
	 */
	private function _getDefaultFilter($get = [])
	{
		$filter = [
			'status'	=> 'active',
			'order_by'	=> 'created_at',
			'order_dir'	=> 'DESC',
		];

		// rubric
		if (!empty($get['rubric'])) {
			$filter['rubric_id'] = intval($get['rubric']);
			$this->vars['currentRubric'] = $filter['rubric_id'];
		}

		if (!empty($get['search']))
			$filter['search'] = trim($get['search']);
		$this->vars['search'] = !empty($filter['search']) ? $filter['search'] : '';

		return $filter;
	}


	public function info()
	{
		$this->vars['current'] = 'info';

		$this->vars['sqlStruct'] = file_get_contents(VF_TPLS_DIR . '/base.sql');

		$this->layout()->page()->setTitle('Описание')->setHeader('Описание');
		$this->tpl()->template('news/info');
	}

	public function refreshRss()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['cnt'] = $this->news()->fill();
		//$this->ajaxResponse['cnt'] = 0;
		die(json_encode($this->ajaxResponse));
	}

	public function clearCache()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->cache()->flush();
		die(json_encode($this->ajaxResponse));
	}

	public function newItemModal()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get("news/_newItemModal", ['rubricTree' => $this->news()->getTree()]);
		die(json_encode($this->ajaxResponse));
	}

	public function refreshTree()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['tree'] = $this->tpl()->get("news/_rubricsList", [
			'rubricTree'	=> $this->news()->getTree(),
			'currentRubric'	=> $this->input()->post('rid', true)
		]);
		die(json_encode($this->ajaxResponse));
	}

	public function newItemSave()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$params = [];
		parse_str($this->input()->post('params'), $params);

		// Check rubrics
		$rubrics = !empty($params['rubrics']) ? $params['rubrics'] : [];
		if (!$rubrics) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Не выбрано ни одной рубрики. WTF???';
			die(json_encode($this->ajaxResponse));
		}

		// Получаем и предобрабатываем входящие поля новости
		$data = !empty($params['news']) ? $params['news'] : [];
		$data = array_map('trim', $data);

		$map = ['header' => 'Название', 'preview' => 'Превьюха', 'content' => 'Контент']; // Для фронта
		foreach ($data as $field => $value) {
			if (!mb_strlen($value)) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = "Не заполнено поле {$map[$field]}";
				die(json_encode($this->ajaxResponse));
			}
		}

		// Когда всё проверили, отдаем дальнейшую работу сервису
		$result = $this->news()->smartCreate($data, $rubrics);
		if (!$result->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $result->getError();
			die(json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Новость добавлена. Подождите 5 сек..';

		die(json_encode($this->ajaxResponse));
	}
}
