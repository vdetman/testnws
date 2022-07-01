<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class NotFoundController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		header('HTTP/1.1 404 Not Found');
		exit('Page not found');
	}
}