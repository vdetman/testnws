<?php namespace News\Entity;

class Rubric
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $sort;

	/**
	 * @var string
	 */
	protected $name;

	public function getId(): ?int {
		return $this->id;
	}

	public function getSort(): ?int  {
		return $this->sort;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function setId(?int $id) {
		$this->id = $id;
		return $this;
	}

	public function setSort(int $sort) {
		$this->sort = $sort;
		return $this;
	}

	public function setName(?string $name) {
		$this->name = $name;
		return $this;
	}

	public function fromArray(array $data)
	{
		if (!empty($data['rubric_id'])) $this->setId($data['rubric_id']);
		if (!empty($data['rubric_sort'])) $this->setSort($data['rubric_sort']);
		if (!empty($data['rubric_name'])) $this->setName($data['rubric_name']);
		return $this;
	}

	public function toArray()
	{
		$data = [];
		if (!is_null($this->getId())) $data['rubric_id'] = $this->getId();
		if (!is_null($this->getSort())) $data['rubric_sort'] = $this->getSort();
		if (!is_null($this->getName())) $data['rubric_name'] = $this->getName();
		return $data;
	}
}
