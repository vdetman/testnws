<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Pagination extends Core
{
	var $total		= 0; // Total number of items (database results)
	var $perPage	= 25; // Max number of items you want shown per page
	var $numLinks	= 4; // Number of "digit" links to show before/after the currently viewed page
	var $page		= 1; // The current page being viewed
	var $full_label	= 'all'; // The current page being viewed

	var $full_link	= 'all';
	var $first_link	= ''; // Текст кнопки "В начало". Если FALSE, то 1
	var $next_link	= '&gt;';
	var $prev_link	= '&lt;';
	var $last_link	= ''; // Текст кнопки "В конец". Если FALSE, то отображается последняя страница

	public $template	= 'pagination';

	public function __construct($params = [])
	{
		if(count($params) > 0){$this->initialize($params);}
	}

	/**
	 * Initialize Preferences
	 * @param	array	initialization parameters
	 */
	function initialize($params = [])
	{
		if(count($params) > 0){
			foreach($params as $key => $val)
				if(isset($this->$key))
					$this->$key = $val;
		}
	}

	/**
	 * Формирует массив элементов навигации и отдаем результат рендеринга шаблона
	 * @return render {template}.tpl
	 */
	public function getHTML()
	{
		$get = $this->input()->get();
		unset($get['page']);// убираем page из GET

		$baseUrl = $this->uri()->getPath();   // формируем базовый URL без GET-параметров

		$num_pages = ceil($this->total / $this->perPage); //Подсчет числа страниц

		/** Корректность ввода номера страницы */
		$this->page !== $this->full_label && $this->page = floor($this->page);
		if($this->page < 1 && $this->page !== $this->full_label)
			Func::redirect($baseUrl.$this->buildGetQuery($get, 1));
		if($this->page !== $this->full_label && $this->page > $num_pages && $num_pages > 1)
			Func::redirect($baseUrl.$this->buildGetQuery($get, $num_pages));

		$result = [];

		if($num_pages < 2) return false;

		/** Элемент "все" */
		$result[] = [
			'name' => $this->full_link,
			'current' => $this->page == $this->full_label,
			'href'  => $baseUrl.$this->buildGetQuery($get, $this->full_label),
			'title' => 'Show all',
		];

		/** Элемент "На первую" */
		if($this->page > $this->numLinks + 1){
			$result[] = [
				'name' => $this->first_link ?: 1,
				'current' => false,
				'href'  => $baseUrl.$this->buildGetQuery($get, '1'),
				'title' => 'To begin',
			];
		}

		/** Элемент "На предыдущую" */
		if($this->page > 1){
			$result[] = [
				'name' => $this->prev_link,
				'current' => false,
				'href'  => $baseUrl.$this->buildGetQuery($get, $this->page - 1),
				'title' => 'Prev',
			];
		}

		/** Ряд предыдущих страниц */
		for($i = $this->numLinks; $i >= 1; $i--){
			if($this->page - $i > 0){
				$result[] = [
					'name' => $this->page - $i,
					'current' => false,
					'href'  => $baseUrl.$this->buildGetQuery($get, $this->page - $i),
					'title' => 'Page '.($this->page - $i),
				];
			}
		}

		/** Текущий элемент */
		if($this->page != $this->full_label){
			$result[] = [
				'name' => $this->page,
				'current' => true,
				'href'  => $baseUrl.$this->buildGetQuery($get, $this->page),
				'title' => '',
			];
		}

		/** Ряд последующих страниц */
		for($i = 1; $i <= $this->numLinks; $i++){
			if($this->page + $i <= $num_pages){
				$cur_page = $this->page == $this->full_label ? 0 : $this->page;
				$result[] = [
					'name' => $cur_page + $i,
					'current' => false,
					'href'  => $baseUrl.$this->buildGetQuery($get, $cur_page + $i),
					'title' => 'Page '.($cur_page + $i),
				];
			}
		}

		/** Элемент "На следущую" */
		if($this->page < $num_pages){
			$result[] = [
				'name' => $this->next_link,
				'current' => false,
				'href'  => $baseUrl.$this->buildGetQuery($get, $this->page + 1),
				'title' => 'Next',
			];
		}

		/** Элемент "На последнюю" */
		if($this->page < ($num_pages - $this->numLinks)){
			$result[] = [
				'name' => $this->last_link ?: $num_pages,
				'current' => false,
				'href'  => $baseUrl.$this->buildGetQuery($get, $num_pages),
				'title' => 'To end',
			];
		}

		$tpl = new Tpl();
		return $tpl->get($this->template, [
			'items' => $result
		]);
	}

	/**
	 * Возвращает строку GET-параметров. Если передан $page, то добавляется/обновляется этот элемент массива
	 * @param array $get
	 * @param string $page
	 * @return string
	 */
	function buildGetQuery($get, $page = false){
		$get = is_array($get) ? $get : [];
		if($page) $get['page'] = $page;
		return $get ? rawurldecode('?'.http_build_query($get)) : '';
	}
}