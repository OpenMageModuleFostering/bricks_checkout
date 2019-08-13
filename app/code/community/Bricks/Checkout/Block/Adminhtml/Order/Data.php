<?php

class Bricks_Checkout_Block_Adminhtml_Order_Data extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

	public function _construct() {
		parent::_construct();
		$this->setTemplate( 'bricks/checkout/order/data.phtml' );
	}

	public function canShowTab() {
		return $this->_hasCustomData();
	}

	public function getTabLabel() {
		return $this->__( 'Custom Checkout Steps' );
	}

	public function getTabTitle() {
		return $this->__( 'Custom Checkout Steps' );
	}

	public function isHidden() {
		return ! $this->_hasCustomData();
	}

	/**
	 * Retrieve order model instance
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder() {
		return Mage::registry( 'current_order' );
	}

	protected  function _hasCustomData() {
		$data = $this->getOrder()->getCustomStepsData();
		return ! empty( $data );
	}

}
