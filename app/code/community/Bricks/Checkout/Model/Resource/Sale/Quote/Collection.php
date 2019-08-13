<?php

class Bricks_Checkout_Model_Resource_Sale_Quote_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	public function _construct() {
		$this->_init( 'bricks_checkout/sale_quote' );
	}

	/**
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @param Bricks_Checkout_Model_Sale_Quote $step
	 * @return Bricks_Checkout_Model_Sale_Quote
	 */
	public function getByQuoteByStep( $quote, $step ) {
		return $this
				->addFieldToFilter( 'step_id', $step->getId() )
				->addFieldToFilter( 'quote_id', $quote->getId() )
				->setPageSize( 1 )
				->setCurPage( 1 )
				->getFirstItem();
	}
}