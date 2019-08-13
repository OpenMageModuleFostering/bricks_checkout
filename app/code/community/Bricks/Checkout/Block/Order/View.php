<?php

class Bricks_Checkout_Block_Order_View extends Mage_Core_Block_Template {
	public function getOrder() {
		return Mage::registry('current_order');
	}

	public function initTotals() {
		$data = $this->getOrder()->getCustomStepsData();
		if ( ! empty( $data ) ) {
			foreach ( $data as $values ) {
				$amount = 0.00;
				foreach ( $values as $value ) {
					$amount += $value['price'];
				}

				$title = Mage::getModel( 'bricks_checkout/sale_order' )->load( $value['sale_order_id'] );
				$this->getParentBlock()->addTotalBefore( new Varien_Object( array(
					'code'	 => $value['sale_order_id'],
					'label'	 => $title->getTitle(),
					'value'	 => $amount
				) ) );
			}
		}
	}
}