<?php namespace News\Entity;

use News\Entity\Rubric;

class Tree
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $itemsCount = 0;

	/**
	 * @var Rubric
	 */
	protected $rubric;

	/**
	 * @var Tree
	 */
	protected $childs = [];

	/**
	 * @var array
	 */
	protected $allChildIds = [];

	public function getId() {
		return $this->id;
	}

	public function getItemsCount(): ?int  {
		return $this->itemsCount;
	}

	/**
	 * @return Rubric
	 */
	public function getRubric(): ?Rubric {
		return $this->rubric;
	}

	public function getChilds(): ?array  {
		return $this->childs;
	}

	public function getAllChildIds(): ?array  {
		return $this->allChildIds;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setItemsCount(int $itemsCount) {
		$this->itemsCount = $itemsCount;
		return $this;
	}

	public function setRubric(Rubric $rubric) {
		$this->rubric = $rubric;
		return $this;
	}

	public function addChild(Tree $child)
	{
		$this->childs[$child->getId()] = $child;

		// Добавляем ID в общий массив
		$this->addAllChildId($child->getId());
		foreach ($child->getAllChildIds() as $childId) {
			$this->addAllChildId($childId);
		}

		return $this;
	}

	public function addAllChildId($allChildId) {
		$this->allChildIds[$allChildId] = $allChildId;
		return $this;
	}
}