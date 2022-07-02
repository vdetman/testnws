<?php namespace News;

use Entity\Filter;

class NewsModel extends \Core
{
	private $table				= 'news';
	private $table_rubrics		= 'news_rubrics';
	private $table_rubrics_tree	= 'news_rubrics_tree';
	private $table_relations	= 'news_relations';

// Items

	/**
	 * @param int
	 * @return array
	 */
	public function get($id)
	{
		$this->db()->prepare("SELECT * FROM {$this->table} WHERE news_id = :id LIMIT 1;");
		$this->db()->execute([':id' => intval($id)]);
		return $this->db()->result();
	}

	/**
	 * @param string
	 * @return boolean
	 */
	public function isExistsHash($hash)
	{
		$this->db()->prepare("SELECT news_id FROM {$this->table} WHERE hash = :hash LIMIT 1;");
		$this->db()->execute([':hash' => $hash]);
		return boolval($this->db()->result());
	}

	/**
	 * @param Filter
	 * @return array
	 */
	public function gets(Filter $filter)
	{
		$join = '';

		// WHERE
		$wh = [];
		if ($filter->get('status')) $wh[] = "n.status IN ({$this->db()->phIn($filter->get('status'))})";
		if ($filter->get('search')) $wh[] = $this->db()->phLike($filter->get('search'), ['n.header','n.preview','n.content']);
		if ($filter->get('rubric_id')) {
			$join = "INNER JOIN {$this->table_relations} r ON r.news_id = n.news_id";
			$wh[] = "r.rubric_id IN ({$this->db()->phIn($filter->get('rubric_id'))})";
		}

		$this->db()->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} n {$join}
		{$this->db()->_where($wh)}
		{$this->db()->_order($filter)}
		{$this->db()->_limit($filter)};");
		$this->db()->execute();
		return $this->db()->results();
	}

	/**
	 * @param array
	 * @return int
	 */
	public function create($data)
	{
		$this->db()->prepare($this->db()->phInsert("INSERT INTO {$this->table} :insert;", $data));
		return $this->db()->execute() ? $this->db()->lastInsertId() : false;
	}

	/**
	 * @param int
	 * @param array
	 * @return boolean
	 */
	public function update($id, $data)
	{
		$this->db()->prepare($this->db()->phSet("UPDATE {$this->table} SET :set WHERE news_id = :id LIMIT 1;", $data));
		return $this->db()->execute([':id' => intval($id)]);
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function delete($id)
	{
		$this->db()->prepare("DELETE FROM {$this->table} WHERE news_id = :id LIMIT 1;");
		return $this->db()->execute([':id' => intval($id)]);
	}

// Relations
	/**
	 * @param array
	 * @return array
	 */
	public function getRelations($partnerIds)
	{
		$this->db()->prepare("SELECT news_id, rubric_id FROM {$this->table_relations} WHERE news_id IN ({$this->db()->phIn($partnerIds)});");
		$this->db()->execute();
		return $this->db()->results();
	}

	/**
	 * @param int
	 * @param int
	 * @return array
	 */
	public function getRelation($partnerId, $rubricId)
	{
		$this->db()->prepare("SELECT * FROM {$this->table_rubrics} WHERE news_id = :pid  AND rubric_id = :sid LIMIT 1;");
		$this->db()->execute([':pid' => intval($partnerId), ':sid' => intval($rubricId)]);
		return $this->db()->result();
	}

	/**
	 * @return array
	 */
	public function getAllRelationsCount()
	{
		$this->db()->prepare("SELECT rubric_id, COUNT(news_id) AS items_count FROM {$this->table_relations} GROUP BY rubric_id;");
		return $this->db()->execute() ? $this->db()->results() : [];
	}

	/**
	 * @param int $partnerId
	 * @param array $rubricIds
	 * @return bool
	 */
	public function deleteRelations($partnerId, $rubricIds)
	{
		$this->db()->prepare("DELETE FROM {$this->table_relations} WHERE news_id = :id AND rubric_id NOT IN ({$this->db()->phIn($rubricIds)});");
		return $this->db()->execute([':id' => intval($partnerId)]);
	}

	/**
	 * @param int
	 * @param int
	 * @return boolean
	 */
	public function createRelation($itemId, $rubricId)
	{
		$this->db()->prepare($this->db()->phInsert("INSERT IGNORE INTO {$this->table_relations} :insert;", ['news_id' => $itemId, 'rubric_id' => $rubricId]));
		return $this->db()->execute();
	}

	/**
	 * @param int
	 * @return bool
	 */
	public function deleteRelationsByItemId($partnerId)
	{
		$this->db()->prepare("DELETE FROM {$this->table_relations} WHERE news_id = :id;");
		return $this->db()->execute([':id' => intval($partnerId)]);
	}

// Rubrics
	/**
	 * @param Filter
	 * @return array
	 */
	public function getRubrics(Filter $filter)
	{
		// WHERE
		$wh = [];
		if ($filter->get('rubric_id')) $wh[] = "rubric_id IN ({$this->db()->phIn($filter->get('rubric_id'))})";
		if ($filter->get('search')) $wh[] = $this->db()->phLike($filter->get('search'), ['rubric_name']);

		$this->db()->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table_rubrics} {$this->db()->_where($wh)} {$this->db()->_order($filter)} {$this->db()->_limit($filter)};");
		$this->db()->execute();
		return $this->db()->results();
	}

	/**
	 * @return array
	 */
	public function getUsedRubrics()
	{
		$this->db()->prepare("SELECT s.rubric_id, s.name, count(DISTINCT r.news_id) as partners
			FROM {$this->table_rubrics} s
			LEFT JOIN {$this->table_relations} r ON r.rubric_id = s.rubric_id
			LEFT JOIN {$this->table} p ON r.news_id = p.news_id
			WHERE s.status = 'active'
			GROUP BY s.rubric_id
			ORDER BY s.rubric_id;");
		return $this->db()->execute() ? $this->db()->results() : [];
	}

	/**
	 * @param int
	 * @return array
	 */
	public function getRubric($id)
	{
		$this->db()->prepare("SELECT * FROM {$this->table_rubrics} WHERE rubric_id = :id LIMIT 1;");
		$this->db()->execute([':id' => intval($id)]);
		return $this->db()->result();
	}

	public function getsForTree()
	{
		$this->db()->prepare("SELECT
			r.rubric_id			AS id,
			r.rubric_name		AS name,
			rt.parent_rubric_id	AS parent_id
		FROM {$this->table_rubrics} r
		INNER JOIN {$this->table_rubrics_tree} rt ON rt.child_rubric_id = r.rubric_id
		ORDER BY r.rubric_sort, r.rubric_id;");
		return $this->db()->execute() ? $this->db()->results() : [];
	}

	/**
	 * @param int
	 * @param array
	 * @return boolean
	 */
	public function updateRubric($id, $data)
	{
		$this->db()->prepare($this->db()->phSet("UPDATE {$this->table_rubrics} SET :set WHERE rubric_id = :id;", $data));
		return $this->db()->execute([':id' => intval($id)]);
	}

	/**
	 * @param array
	 * @return int
	 */
	public function createRubric($data)
	{
		$this->db()->prepare($this->db()->phInsert("INSERT INTO {$this->table_rubrics} :insert;", $data));
		return $this->db()->execute() ? $this->db()->lastInsertId() : false;
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function deleteRubric($id)
	{
		$this->db()->prepare("DELETE FROM {$this->table_rubrics} WHERE rubric_id = :id;");
		return $this->db()->execute([':id' => intval($id)]);
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function addRubricToRootTree($rubricId)
	{
		$this->db()->prepare($this->db()->phInsert("INSERT IGNORE INTO {$this->table_rubrics_tree} :insert;", ['parent_rubric_id' => NULL, 'child_rubric_id' => $rubricId]));
		return $this->db()->execute();
	}
}
