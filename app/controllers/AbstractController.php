<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

abstract class AbstractController extends Core
{
	public $ajaxResponse	= ['status' => true, 'descr' => 'empty']; //массив с результатами Ajax-запроса
	public $vars			= []; //Переменные шаблона
	public $addJs			= []; //Массив дополнительно подгружаемых JS файлов
	public $addCss			= []; //Массив дополнительно подгружаемых CSS файлов
	public $addMeta			= []; //Массив дополнительных мета тегов

	public function __destruct()
	{
		//additional tpl vars
		$this->vars['this'] = $this; //$this
		$this->vars['addJs'] = $this->addJs;
		$this->vars['addCss'] = $this->addCss;
		$this->vars['addMeta'] = $this->addMeta;
		$this->vars['layout'] = $this->layout();
		$this->vars['module'] = !empty($this->module) ? $this->module : '';

		//Вывод результата
		$this->tpl()->display($this->vars);
	}

	public function __construct()
	{
		$this->_processDisposition(); // Режим работы сайта
		$this->vars['current'] = null;
	}

	/**
	 * @return \Auth\Entity\User
	 */
	public function currentUser()
	{
		return $this->auth()->getUser();
	}

	/**
	 * Обработка режима работы сайта
	 * @access private
	 */
	private function _processDisposition()
	{
		switch ($this->uri()->getSegments(0)) {
			default: define('DISPOSITION', false);
		}
	}

	protected function _notify($text, $status = null)
	{
		$status = is_null($status) ? '1' : (boolval($status) ? '1' : '0');
		$this->session()->setFlash('_notify', ['text' => $text, 'status' => $status]);
		return boolval($status);
	}

	//Удаление устаревших файлов
	protected function _clean_tmp_files()
	{
		$protectedFiles = ['.htaccess','queue_list.ini','jwt_token_client.json'];

		$directories = [
			VF_TMP_DIR, // Временные файлы
		];

		foreach($directories as $directory)
		{
			$fp = opendir($directory);
			while($file = readdir($fp)) {
				if(is_file($directory.'/'.$file) && !in_array($file, $protectedFiles)){
					if((time() - filemtime($directory.'/'.$file)) > 3600) // 1 час
						@unlink($directory.'/'.$file);
				}
			}
			closedir($fp);
		}
	}

	//Отображение страницы 404
	protected function show404()
	{
		setStatusHeader(404);
		trigger_error('Page Not Found ('.$this->input()->server('REQUEST_URI').')', E_USER_ERROR);
		$this->layout()->pages()->set('p404');
		$this->tpl()->directory('');//Сброс директории шаблонов
		$this->tpl()->template('p404');
		die();
	}
}