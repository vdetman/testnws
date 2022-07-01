<?php namespace News\Entity;

use Entity\DateTime;

class Item
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $header;

	/**
	 * @var string
	 */
	protected $preview;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var DateTime
	 */
	protected $created;

	public function getId(): ?int {
		return $this->id;
	}

	public function getHeader(): ?string  {
		return $this->header;
	}

	public function getPreview(): ?string  {
		return $this->preview;
	}

	public function getContent(): ?string  {
		return $this->content;
	}

	public function getHash(): ?string  {
		return $this->hash;
	}

	public function getStatus(): ?string {
		return $this->status;
	}

	public function getCreated(): ?DateTime {
		return $this->created;
	}

	public function setId(int $id) {
		$this->id = $id;
		return $this;
	}

	public function setHeader(string $header) {
		$this->header = $header;
		return $this;
	}

	public function setPreview(string $preview) {
		$this->preview = $preview;
		return $this;
	}

	public function setContent(string $content) {
		$this->content = $content;
		return $this;
	}

	public function setHash(string $hash) {
		$this->hash = $hash;
		return $this;
	}

	public function setStatus(string $status) {
		$this->status = $status;
		return $this;
	}

	public function setCreated(DateTime $created) {
		$this->created = $created;
		return $this;
	}

	public function fromArray(array $data)
	{
		if (!empty($data['news_id'])) $this->setId($data['news_id']);
		if (!empty($data['header'])) $this->setHeader($data['header']);
		if (!empty($data['preview'])) $this->setPreview($data['preview']);
		if (!empty($data['content'])) $this->setContent($data['content']);
		if (!empty($data['hash'])) $this->setHash($data['hash']);
		if (!empty($data['status'])) $this->setStatus($data['status']);
		if (!empty($data['created_at'])) $this->setCreated(new DateTime($data['created_at']));
		return $this;
	}

	public function toArray()
	{
		$data = [];
		if (!is_null($this->getId())) $data['news_id'] = $this->getId();
		if (!is_null($this->getHeader())) $data['header'] = $this->getHeader();
		if (!is_null($this->getPreview())) $data['preview'] = $this->getPreview();
		if (!is_null($this->getContent())) $data['content'] = $this->getContent();
		if (!is_null($this->getHash())) $data['hash'] = $this->getHash();
		if (!is_null($this->getStatus())) $data['status'] = $this->getStatus();
		if (!is_null($this->getCreated())) $data['created_at'] = $this->getCreated() ? $this->getCreated()->format(SQL_TIMESTAMP_FORMAT) : null;
		return $data;
	}
}