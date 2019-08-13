<?php

class Bricks_Checkout_Model_Resource_Field_Value extends Mage_Core_Model_Resource_Db_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/field_value', 'field_value_id' );
	}
}