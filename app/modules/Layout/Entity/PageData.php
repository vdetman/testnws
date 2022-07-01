<?php namespace Layout\Entity;

class PageData
{
	/**
	 * @var int
	 */
	protected $pageId;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $header;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $robots;

	/**
	 * @var string
	 */
	protected $keywords;

	/**
	 * @var string
	 */
	protected $description;

	public function getPageId() {
		return $this->pageId;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getHeader() {
		return $this->header;
	}

	public function getContent() {
		return $this->content;
	}

	public function getRobots() {
		return $this->robots;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setPageId($pageId) {
		$this->pageId = $pageId;
		return $this;
	}

	public function setLanguage($language) {
		$this->language = $language;
		return $this;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function setHeader($header) {
		$this->header = $header;
		return $this;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function setRobots($robots) {
		$this->robots = $robots;
		return $this;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
		return $this;
	}

	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	public function fromArray(array $data)
	{
		if(!empty($data['PageId'])) $this->setPageId($data['PageId']);
		if(!empty($data['Language'])) $this->setLanguage($data['Language']);
		if(!empty($data['title'])) $this->setTitle($data['title']);
		if(!empty($data['Header'])) $this->setHeader($data['Header']);
		if(!empty($data['Content'])) $this->setContent($data['Content']);
		if(!empty($data['Robots'])) $this->setRobots($data['Robots']);
		if(!empty($data['Keywords'])) $this->setKeywords($data['Keywords']);
		if(!empty($data['description'])) $this->setDescription($data['description']);

		return $this;
	}

	public function toArray()
	{
		$data = [];

		if(null !== $this->getPageId()) $data['PageId'] = $this->getPageId();
		if(null !== $this->getLanguage()) $data['Language'] = $this->getLanguage();
		if(null !== $this->getTitle()) $data['title'] = $this->getTitle();
		if(null !== $this->getHeader()) $data['Header'] = $this->getHeader();
		if(null !== $this->getContent()) $data['Content'] = $this->getContent();
		if(null !== $this->getRobots()) $data['Robots'] = $this->getRobots();
		if(null !== $this->getKeywords()) $data['Keywords'] = $this->getKeywords();
		if(null !== $this->getDescription()) $data['description'] = $this->getDescription();

		return $data;
	}
}