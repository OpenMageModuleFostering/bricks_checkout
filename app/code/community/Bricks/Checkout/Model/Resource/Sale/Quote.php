<?php

class Bricks_Checkout_Model_Resource_Sale_Quote extends Mage_Core_Model_Resource_Db_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/sale_quote', 'sale_quote_id' );
	}

	public function deleteByQuote( $quote, $step ) {
		$this->_getWriteAdapter()->delete( $this->getMainTable(), array(
			'quote_id = ?'	 => $quote->getId(),
			'step_id = ?'	 => $step->getId()
		) );
		return $this;
	}
}