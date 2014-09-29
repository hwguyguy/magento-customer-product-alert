<?php
class Hwguyguy_CustomerProductAlert_Block_View extends Mage_Core_Block_Template {
	/**
	 * @type array|null $stockProducts
	 */
	protected $stockProducts = null;

	/**
	 * @type array|null $priceProducts
	 */
	protected $priceProducts = null;

	/**
	 * @param array $productIds
	 * @param array
	 */
	protected function getProductCollectionByIds($productIds = array()) {
		$products = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addAttributeToSelect(array('name', 'image', 'small_image', 'thumbnail'));

		$idProducts = array();
		foreach ($products as $product) {
			$idProducts[$product->getId()] = $product;
		}

		$products = array();
		foreach ($productIds as $id) {
			if (!isset($idProducts[$id])) { continue; }
			$products[] = $idProducts[$id];
		}

		return $products;
	}

	/**
	 * @return array
	 */
	public function getStockProducts() {
		if ($this->stockProducts) {
			return $this->stockProducts;
		}

		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$alerts = Mage::getModel('productalert/stock')->getCollection()
			->addFieldToFilter('customer_id', $customerId);

		$productIds = array();
		foreach ($alerts as $alert) {
			$productIds[] = $alert->getProductId();
		}

		$this->stockProducts = $this->getProductCollectionByIds($productIds);
		return $this->stockProducts;
	}

	/**
	 * @return array
	 */
	public function getPriceProducts() {
		if ($this->priceProducts) {
			return $this->priceProducts;
		}

		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$alerts = Mage::getModel('productalert/price')
			->getCollection()
			->addFieldToFilter('customer_id', $customerId);

		$productIds = array();
		foreach ($alerts as $alert) {
			$productIds[] = $alert->getProductId();
		}

		$this->priceProducts = $this->getProductCollectionByIds($productIds);
		return $this->priceProducts;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getProductUrl($product) {
		if ($product->getVisibleInSiteVisibilities()) {
			return $product->getUrlModel()->getUrl($product);
		}
		return '#';
	}
}
