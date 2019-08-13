<?php

class Bricks_Checkout_Block_Adminhtml_Order_Invoice extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals {
	protected function _initTotals() {
		parent::_initTotals();

		$data = $this->getOrder()->getCustomStepsData();
		if ( ! empty( $data ) ) {
			foreach ( $data as $id => $values ) {
				$amount = 0;
				foreach ( $values as $value )
					$amount += $value['price'];

				$title = Mage::getSingleton('bricks_checkout/sale_order')->load( $value['sale_order_id'] );

				$this->addTotalBefore( new Varien_Object( array(
					'code'		 => 'step-' . $id,
					'value'		 => $amount,
					'base_value' => $amount,
					'label'		 => $title->getTitle(),
				) ) );
			}
		}

		return $this;
	}
}