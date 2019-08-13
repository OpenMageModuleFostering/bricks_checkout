<?php

class Bricks_Checkout_Model_Resource_Field extends Mage_Core_Model_Resource_Db_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/field', 'field_id' );
	}

	public function deleteValues( Mage_Core_Model_Abstract $object ) {
		$this->_getWriteAdapter()->delete (
			$this->getTable( 'bricks_checkout/field_value' ),
			array( 'field_id = ?' => $object->getId() )
		);

		return $this;
	}

}