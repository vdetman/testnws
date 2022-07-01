<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class IndexController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		$this->vars['current'] = 'main';
	}

	public function index()
	{
		Func::redirect('/news');
	}
}