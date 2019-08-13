<?php

class Bricks_Checkout_Model_Resource_Sale_Order extends Mage_Core_Model_Resource_Db_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/sale_order', 'sale_order_id' );
	}
}