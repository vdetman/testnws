<?php namespace Layout\Entity;

class CurrentPage
{
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
		if(!empty($data['title'])) $this->setTitle($data['title']);
		if(!empty($data['Header'])) $this->setHeader($data['Header']);
		if(!empty($data['Content'])) $this->setContent($data['Content']);
		if(!empty($data['Robots'])) $this->setRobots($data['Robots']);
		if(!empty($data['Keywords'])) $this->setKeywords($data['Keywords']);
		if(!empty($data['description'])) $this->setDescription($data['description']);

		return $this;
	}
}