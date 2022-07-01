<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;

class TariffsController extends AbstractControlController
{
	protected $module = 'tariffs';

	public function __construct()
	{
		parent::__construct();

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		$this->vars['module'] = $this->module;
	}

	public function index()
	{
		$marketplaces = $this->marketplaces()->gets(new Filter());
		$this->vars['marketplaces'] = $marketplaces;

		$mp = $this->input()->get('marketplace', true);
		if (!array_key_exists($mp, $marketplaces))
			Func::redirect('/' . ADMIN . '/' . $this->module . '?marketplace=' . reset(array_keys($marketplaces)) . '&tmp=no');
		$marketplace = $marketplaces[$mp];
		$this->vars['marketplace'] = $marketplace;

		$this->vars['modules'] = $this->marketplaces()->getModules(new Filter(['status' => 'active']));

		$this->vars['tariffs'] = $this->finance()->tariffs()->gets(new Filter(['marketplace_id' => $marketplace->getId(), 'order_by' => 'module_id, min_items NULLS FIRST']));

		$this->addCss[] = '/admin/css/modules/tariffs.css'.Func::modifyTime('/admin/css/modules/tariffs.css');
		$this->addJs[] = '/admin/js/modules/tariffs.js'.Func::modifyTime('/admin/js/modules/tariffs.js');

		$this->layout()->page()->setHeader('Тарифы');
		$this->tpl()->template($this->module.'/index');
	}

	public function create()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		// Права доступа на U-operation
		if (!$this->_allowWrite()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'У Вас нет прав для этой операции';
			die (json_encode($this->ajaxResponse));
		}

		$mp = $this->marketplaces()->get(intval($this->input()->post('marketplace_id', true)));
		if (!$mp) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Не указан Marketplace';
			die (json_encode($this->ajaxResponse));
		}

		$t = $this->finance()->tariffs()->_new()->setPriceType('items')->setMarketplaceId($mp->getId())->setModuleId(1);
		if (!$this->finance()->tariffs()->create($t)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Ошибка';
			die(json_encode($this->ajaxResponse));
		}

		die(json_encode($this->ajaxResponse));
	}

	public function update()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		// Права доступа на U-operation
		if (!$this->_allowWrite()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'У Вас нет прав для этой операции';
			die (json_encode($this->ajaxResponse));
		}

		$tariff = $this->finance()->tariffs()->get(intval($this->input()->post('TariffId', true)));
		if (!$tariff) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Запись не найдена';
			die (json_encode($this->ajaxResponse));
		}

		$module = $this->marketplaces()->getModule(intval($this->input()->post('ModuleId', true)));
		if (!$module) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Неверный модуль';
			die (json_encode($this->ajaxResponse));
		}

		$minItems = intval($this->input()->post('MinItems', true));
		$maxItems = intval($this->input()->post('MaxItems', true));
		$priceType = trim($this->input()->post('PriceType', true));
		$price = floatval($this->input()->post('Price', true));
		$minBalance = floatval($this->input()->post('MinBalance', true));

		if (!in_array($priceType, ['items', 'period'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Неверный тип цены';
			die (json_encode($this->ajaxResponse));
		}

		$t = $this->finance()->tariffs()->_new()
			->setMinItems($minItems)
			->setMaxItems($maxItems)
			->setPrice($price)
			->setPriceType($priceType)
			->setMinBalance($minBalance)
			->setModuleId($module->getId());

		if (!$this->finance()->tariffs()->update($tariff->getId(), $t->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Ошибка';
			die(json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Тариф успешно сохранен';

		die(json_encode($this->ajaxResponse));
	}

	public function delete()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		// Права доступа на U-operation
		if (!$this->_allowWrite()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'У Вас нет прав для этой операции';
			die (json_encode($this->ajaxResponse));
		}

		if (false == ($item = $this->finance()->tariffs()->get(intval($this->input()->post('id', true))))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Запись не найдена';
			die (json_encode($this->ajaxResponse));
		}

		if (!$this->finance()->tariffs()->delete($item->getId())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['error'] = 'Ошибка';
			die(json_encode($this->ajaxResponse));
		}

		die(json_encode($this->ajaxResponse));
	}

	private function _allowWrite($roles = [])
	{
		return in_array($this->currentUser()->getRole(), array_merge($roles, ['root','admin']));
	}
}