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

		if (!empty($get['search'])) {
			$filter['search'] = trim($get['search']);
			$this->vars['search'] = $filter['search'];
		}

		return $filter;
	}


	public function info()
	{
		$this->vars['current'] = 'info';
		$this->layout()->page()->setTitle('Описание')->setHeader('Описание');
		$this->tpl()->template('news/info');
	}

	public function refreshRss()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->ajaxResponse['cnt'] = $this->news()->fill();
		die(json_encode($this->ajaxResponse));
	}

	public function clearCache()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();
		$this->cache()->flush();
		die(json_encode($this->ajaxResponse));
	}
}
