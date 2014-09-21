<?php
class Hwguyguy_CustomerProductAlert_IndexController extends Mage_Core_Controller_Front_Action {
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function getSession() {
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch() {
		parent::preDispatch();

		if (!$this->getRequest()->isDispatched()) {
			return;
		}

		$action = $this->getRequest()->getActionName();
		$openActions = array(
			'dummy',
		);
		$pattern = '/^(' . implode('|', $openActions) . ')/i';

		if (!preg_match($pattern, $action)) {
			if (!$this->getSession()->authenticate($this)) {
				$this->setFlag('', 'no-dispatch', true);
			}
		} else {
			$this->getSession()->setNoReferer(true);
		}
	}

	/**
	 * Action postdispatch
	 *
	 * Remove No-referer flag from customer session after each action
	 */
	public function postDispatch() {
		parent::postDispatch();
		$this->getSession()->unsNoReferer(false);
	}

	public function indexAction() {
		$this->loadLayout();

		if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
			$navigationBlock->setActive('customer-product-alert');
		}

		$this->renderLayout();
	}
}
