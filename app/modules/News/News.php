<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use News\Entity\Item;
use News\Entity\Tree;
use News\Entity\Rubric;
use News\NewsModel as Model;

class News extends Core
{
	public function refresh()
	{
		$this->cache()->deleteByTag(CACHE_NEWS_TAG);
	}

	/**
	 * Заполнение списка новостей из RSS lenta.ru
	 * @return int
	 */
	public function fill()
	{
		$rss = 'https://lenta.ru/rss';
		$xml = simplexml_load_string(file_get_contents($rss), "SimpleXMLElement", LIBXML_NOCDATA);
		$data = json_decode(json_encode($xml), true);

		$previewLimit = 100;
		$items = $categories = $hashes = [];
		foreach (!empty($data['channel']['item']) ? $data['channel']['item'] : [] as $aItem) {

			if (empty($aItem['category'])) continue; // без категории сразу нах..

			// Собираем в кучку все категории (рубрики). Потом добавим их в БД
			$categories[$aItem['category']] = $aItem['category'];

			$descLength = mb_strlen($aItem['description']);

			// Собараем очищенные данные для новостей
			$item = [
				'header'		=> $aItem['title'],
				'preview'		=> $descLength >= $previewLimit ? trim(mb_substr($aItem['description'], 0, $previewLimit)) . '...' : trim($aItem['description']),
				'content'		=> trim($aItem['description']),
				'created_at'	=> date(SQL_TIMESTAMP_FORMAT, strtotime($aItem['pubDate'])),
				'category'		=> $aItem['category'],
			];
			$items[] = $item;

			// Собираем хэши, чтобы разом проверить дубли
			$hashes[] = $this->_getHash($item['header'], date('Y-m-d', strtotime($item['created_at'])));
		}

		// Получаем текущие рубрики в виде мэппинга
		$rubricName2Id = [];
		foreach ($this->getRubrics(new Filter()) as $rubric)
			$rubricName2Id[$rubric->getName()] = $rubric->getId();


		// Добавляем рубрики в БД, параллельно обновляем мэппинг
		foreach ($categories as $rubricName) {

			// Если уже есть, то пропускаем нах..
			if (array_key_exists($rubricName, $rubricName2Id)) continue;

			$this->db()->begin();

			// Добавляем рубрику
			$nRub = $this->_newRubric()->setName($rubricName);
			if (false == ($rubricId = $this->createRubric($nRub))) {
				$this->db()->rollback();
				continue;
			}

			// Добавляем рубрику в общее дерево
			if (!$this->addRubricToRootTree($rubricId)) {
				$this->db()->rollback();
				continue;
			}

			$this->db()->commit();

			$rubricName2Id[$rubricName] = $rubricId;
		}

		// Получаем текущие новости по полученным хэшам
		$existsHashes = [];
		foreach ($this->gets(new Filter(['hash' => $hashes])) as $n)
			$existsHashes[] = $n->getHash();

		// Добавляем сами новости
		$created = 0;
		$items = array_reverse($items); // Пытаемся сохранить логичность news_id → created_at
		foreach ($items as $aItem) {

			$item = $this->_new()->fromArray($aItem)->setStatus('active');
			$item->setHash($this->_getHash($item->getHeader(), $item->getCreated() ? $item->getCreated()->format('Y-m-d') : ''));

			// Если уже есть, то пропускаем нах..
			if (in_array($item->getHash(), $existsHashes)) continue;

			$this->db()->begin();

			if (false == ($itemId = $this->create($item))) {
				$this->db()->rollback();
				continue;
			}

			// Привяжем к рубрике
			if (!empty($rubricName2Id[$aItem['category']]) && !$this->createRelation($itemId, $rubricName2Id[$aItem['category']])) {
				$this->db()->rollback();
				continue;
			}

			$this->db()->commit();
			$created++;
		}

		return $created;
	}

	/**
	 * @return Item
	 */
	public function _new() {
		return new Item();
	}

	/**
	 * @param int
	 * @return Item|null
	 */
	public function get($id = false)
	{
		$item = null;
		if (false != ($info = $this->_model()->get($id)))
			$item = (new Item())->fromArray($info);
		return $item;
	}

	/**
	 * @param Filter
	 * @return Item
	 */
	public function gets(Filter $filter)
	{
		$result = [];
		foreach ($this->_model()->gets($filter) as $info) {
			$item = (new Item())->fromArray($info);
			$result[$item->getId()] = $item;
		}
		$this->_setTotal($this->db()->totalRows());
		return $result;
	}

	/**
	 * @param int
	 * @param array
	 * @return boolean
	 */
	public function update($id, array $data)
	{
		if ($this->_model()->update($id, $data)) {
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param Item
	 * @return boolean
	 */
	public function create(Item $item)
	{
		// Формируем HASH, чтобы дубли не залетали
		if (!$item->getHash())
			$item->setHash($this->_getHash($item->getHeader(), $item->getCreated() ? $item->getCreated()->format('Y-m-d') : ''));

		if ($lastId = $this->_model()->create($item->toArray())) {
			$this->refresh();
			return $lastId;
		}
		return false;
	}


	/**
	 * @param array
	 * @param array
	 * @return Result
	 */
	public function smartCreate($data, $rubrics)
	{
		$result = new Result();

		// Инстанс новости
		$item = $this->_new()->fromArray($data)->setStatus('active')->setCreated(new \Entity\DateTime());
		$item->setHash($this->_getHash($item->getHeader(), $item->getCreated() ? $item->getCreated()->format('Y-m-d') : ''));

		if ($this->_model()->isExistsHash($item->getHash()))
			return $result->setError('Такая новость уже есть в БД');

		$this->db()->begin();

		if (false == ($itemId = $this->create($item))) {
			$this->db()->rollback();
			return $result->setError('Ошибка добавления новости');
		}

		// Привяжем к рубрике
		foreach ($rubrics as $rubricId) {
			if (!$this->createRelation($itemId, $rubricId)) {
				$this->db()->rollback();
				return $result->setError("Ошибка привязки новости к рубрике #{$rubricId}");
			}
		}
		
		$this->db()->commit();

		return $result->setStatus(true);
	}

	/**
	 * @param string
	 * @param string
	 * @return string(32)
	 */
	private function _getHash($header, $date)
	{
		return md5(mb_strtolower($header) . '~' . $date);
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function delete($id)
	{
		if ($this->_model()->delete($id)) {
			$this->_model()->deleteRelationsByItemId($id);
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param array
	 * @return boolean
	 */
	public function multiUpdate(array $data)
	{
		if (!$data) return false;
		foreach ($data as $k => $v)
			if (!$this->_model()->update($k, $this->_new()->fromArray($v)->toArray()))
				return false;
		return true;
	}

// Relations
	/**
	 * @param array
	 * @return array
	 */
	public function getRelations($partnerIds = [])
	{
		$result = [];
		$relations = $this->_model()->getRelations($partnerIds);
		foreach ($relations as $r)
			$result[$r['partner_id']][$r['sphere_id']] = $r['sphere_id'];
		return $result;
	}

	/**
	 * @param int
	 * @param int
	 * @return boolean
	 */
	public function createRelation($itemId, $rubricId)
	{
		if ($this->_model()->createRelation($itemId, $rubricId)) {
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param int
	 * @param array
	 * @return boolean
	 */
	public function setRubrics($partnerId, $spheres)
	{
		// Удаляем все связи
		if (!$this->_model()->deleteRelationsByItemId($partnerId))
			return false;

		foreach ($spheres ?: [] as $sphereId) {
			if (!$this->_model()->createRelation($partnerId, $sphereId))
				return false;
		}

		return true;
	}
// Rubrics

	/**
	 * @return Rubric
	 */
	public function _newRubric()
	{
		return new Rubric();
	}

	/**
	 * @param Filter
	 * @return Rubric
	 */
	public function getRubrics(Filter $filter)
	{
		$result = [];
		foreach ($this->_model()->getRubrics($filter) as $info) {
			$item = $this->_newRubric()->fromArray($info);
			$result[$item->getId()] = $item;
		}
		$this->_setTotal($this->db()->totalRows());
		return $result;
	}

	/**
	 * @param false $id
	 * @return array
	 */
	public function getRubric($id = false)
	{
		$item = null;
		if (false != ($info = $this->_model()->getRubric($id)))
			$item = $this->_fillRubric($info);
		return $item;
	}

	/**
	 * @param int
	 * @param array
	 * @return boolean
	 */
	public function updateRubric($id, array $data)
	{
		if ($this->_model()->updateRubric($id, $data)) {
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param Rubric
	 * @return boolean
	 */
	public function createRubric(Rubric $item)
	{
		if ($lastId = $this->_model()->createRubric($item->toArray())) {
			$this->refresh();
			return $lastId;
		}
		return false;
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function deleteRubric($id)
	{
		if ($this->_model()->deleteRubric($id)) {
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param int
	 * @return boolean
	 */
	public function addRubricToRootTree($id)
	{
		if ($this->_model()->addRubricToRootTree($id)) {
			$this->refresh();
			return true;
		}
		return false;
	}

	/**
	 * @param int $partnerId
	 * @param int $sphereId
	 * @return bool
	 */
	public function checkRelationExists(int $partnerId, int $sphereId)
	{
		if (!empty($this->_model()->getRelation($partnerId, $sphereId))) {
			return true;
		}
		return false;
	}

	/**
	 * @param int $partnerId
	 * @param array $sphereIds
	 * @return bool
	 */
	public function checkAndRemoveRelations(int $partnerId, array $sphereIds)
	{
		if (!empty($this->_model()->deleteRelations($partnerId, $sphereIds))) {
			return true;
		}
	}

	/**
	 * @param array
	 * @return Rubric
	 */
	private function _fillRubric(array $info)
	{
		return (new Rubric())->fromArray($info);
	}

// Tree
	private $allRubrics; // Все рубрики
	private $childs; // Map ['id' => [2,3,4...100]]
	private $itemsCounts; // Counts ['id' => 25,..]

	/**
	 * Формируем дерево рубрик
	 * @return Tree
	 */
	public function getTree($rootId = false)
	{
		if (CACHE_NEWS_DISABLE || false == ($tree = $this->cache()->get(CACHE_NEWS_RUBRICS))) {

			// В цикле формируем массивы
			if (is_null($this->allRubrics)) {
				foreach ($this->_model()->getsForTree() as $r) {
					$this->allRubrics[$r['id']] = $r; // Ключом будет id категории
					$this->childs[intval($r['parent_id'])][$r['id']] = $r; // Ключом будет ID спонсора
				}
			}

			// Получаем массив кол-ва новостей по рубрикам
			foreach ($this->_model()->getAllRelationsCount() as $r)
				$this->itemsCounts[$r['rubric_id']] = $r['items_count'];

			$rootId = intval($rootId);
			$tree[$rootId] = [];
			$this->_buildTree($rootId, $tree[$rootId]);

			$tree = $this->_getTreeAsObject($tree[$rootId]);

			$this->cache()->set(CACHE_NEWS_RUBRICS, $tree, CACHE_EXPIRY, [CACHE_NEWS_TAG]);
		}

		return $tree ?: new Tree();
	}

	private function _buildTree($parentId, &$tree)
	{
		// Данные текущего элемента
		$tree = [
			'id'		=> $parentId,
			'data'		=> isset($this->allRubrics[$parentId]) ? $this->allRubrics[$parentId] : null,
			'childs'	=> [],
		];

		// Проверяем наличие дочек
		if (isset($this->childs[$parentId])) {
			foreach ($this->childs[$parentId] as $rubric) {
				$this->_buildTree($rubric['id'], $tree['childs'][$rubric['id']]);
			}
		}
	}

	private function _getTreeAsObject($tree)
	{
		$object = (new Tree())->setId($tree['id']);
		$object->setItemsCount(!empty($this->itemsCounts[$object->getId()]) ? $this->itemsCounts[$object->getId()] : 0);

		if ($object->getId()) {
			$object->setRubric($this->_newRubric()->fromArray([
				'rubric_id'		=> $tree['data']['id'],
				'rubric_name'	=> $tree['data']['name']
			]));
		}

		// Проверяем наличие дочек
		foreach ($tree['childs'] as $subTree)
			$object->addChild($this->_getTreeAsObject($subTree));

		return $object;
	}
// -END Tree

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
	private $model;
	private $total = 0;

	/**
	 * @return Model
	 */
	protected function _model()
	{
		return is_null($this->model) ? $this->model = new Model() : $this->model;
	}

	public function getTotal() {return $this->total;}
	private function _setTotal($total) {$this->total = intval($total);}
}
