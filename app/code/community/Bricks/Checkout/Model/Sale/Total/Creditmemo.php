<?php

class Bricks_Checkout_Model_Sale_Total_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {

	public function collect( Mage_Sales_Model_Order_Creditmemo $creditmemo ) {
		parent::collect($creditmemo);

		$data = $creditmemo->getOrder()->getCustomStepsData();
		if ( ! empty( $data ) ) {
			$amount = 0;
			foreach( $data as $value ) {
				foreach ( $value as $val )
					$amount += $val['price'];
			}

			$creditmemo->setGrandTotal(  $amount + $creditmemo->getGrandTotal() );
			$creditmemo->setBaseGrandTotal( $amount + $creditmemo->getBaseGrandTotal() );
		}

		return $this;
	}

}
