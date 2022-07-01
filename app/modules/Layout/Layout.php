<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Layout\Entity\CurrentPage;
use Layout\Entity\PageData;

class Layout extends Core
{
	private $currentPage;

	/**
	 * @return CurrentPage
	 */
	public function page()
	{
		if (null === $this->currentPage) $this->currentPage = new CurrentPage();
		return $this->currentPage;
	}

	/**
	 * @param PageData
	 * @return boolean
	 */
	public function setPage(PageData $data)
	{
		$currentPage = new CurrentPage();
		$this->currentPage = $data ? $currentPage->fromArray($data->toArray()) : $this->currentPage;
		return true;
	}
}