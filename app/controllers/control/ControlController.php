<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class ControlController extends AbstractControlController
{
	protected $module = 'index';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = '';
	}

	public function index()
	{
		$this->addJs[] = '/admin/js/modules/index.js'.Func::modifyTime('/admin/js/modules/index.js');

		$this->layout()->page()->setHeader('Рабочий стол');
		$this->tpl()->template($this->module.'/index');
	}

	public function clearCache()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		if (!$this->cache()->clear()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка сброса КЭШа';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'КЭШ сброшен';

		die (json_encode($this->ajaxResponse));
	}

	public function loginByUser()
    {
        //Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$user = $this->users()->get(intval($this->input()->post('uid', true)));
        if (!$user) {
            $this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'User not found';
            die(json_encode($this->ajaxResponse));
        }

		$data = ['uid' => $user->getId(), 'exp' => time() + 300];
		$data['token'] = hash('sha512', implode('!~!~!', $data));
		$link = FRONTEND_HOST . "/api/externalAuth/?" . http_build_query($data);
		if (!$link) {
			$this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'Auth error';
            die(json_encode($this->ajaxResponse));
		}

        $this->ajaxResponse['redirect'] = $link;
        die(json_encode($this->ajaxResponse));
    }

	public function showUserFinance()
    {
        //Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$user = $this->users()->get(intval($this->input()->post('uid', true)));
		if (!$user) {
            $this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'User not found';
            die(json_encode($this->ajaxResponse));
        }

		$uf = $this->finance()->operations()->getUserFinance(intval($this->input()->post('uid', true)));
        if (!$uf) {
            $this->ajaxResponse['status'] = false;
            $this->ajaxResponse['error'] = 'FinanceInfo not found';
            die(json_encode($this->ajaxResponse));
        }
		$this->ajaxResponse['modalContainer'] = $this->tpl()->get('_units/_userFinance', ['user' => $user, 'uf' => $uf]);
        die(json_encode($this->ajaxResponse));
    }

	/**
	 * Возвращает варианты автокомплита
	 */
	public function findUsers()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$query = trim($this->input()->post('query', true));
		$query = preg_replace("~[[:space:]]+~i", ' ', $query); // 1 space only

		$result['suggestions'] = [];

		foreach($this->users()->gets(new \Entity\Filter(['search' => $query, 'order_by' => 'user_id', 'order_dir' => 'ASC', 'limit' => 25])) as $u){
			$result['suggestions'][] = [
				'value' => '#' . $u->getId() . ' / ' . $u->getName() . ' / ' . $u->getEmail(),
				'data'  => [
					'id' => $u->getId(),
					'name' => $u->getName(),
					'email' => $u->getEmail()
				],
			];
		}

		die( (string) json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Получаем файл в динамике
	 * @param string
	 * @return header
	 */
	public function file($hash = false)
	{
		$file = $this->local()->getByHash($hash);
		if(!$file) return false;

		$filePath = $this->local()->pathToFile($file->getName()); //Полный путь
		if(!is_file($filePath)) return false;

		if ($file->getMime() && is_file($filePath)) {
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($filePath)) {
				header('HTTP/1.0 304 Not Modified');
				header("Cache-Control: max-age=12096000, public");
				header("Expires: " . gmdate(DATE_RFC822, time() + 12096000));
				header("Pragma: cache");
				die();
			}else{
				header("Content-type: ".$file->getMime());
				header('Content-Length: ' . filesize($filePath));
				header("Cache-Control: max-age=12096000, public");
				header("Expires: " . gmdate('D, d M Y H:i:s', time() + 12096000).' GMT');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)).' GMT');
				header("Pragma: cache");
				die ( @file_get_contents($filePath) );
			}
		}
		die();
	}
}