<?php

class Bricks_Checkout_Model_Resource_Field_Price {
	public function toOptionArray() {
		return array(
			array( 'value' => 'fixed', 'label' => Mage::helper( 'bricks_checkout' )->__( 'Fixed' ) ),
			array( 'value' => 'percent', 'label' => Mage::helper( 'bricks_checkout' )->__( 'Percent' ) )
		);
	}
}