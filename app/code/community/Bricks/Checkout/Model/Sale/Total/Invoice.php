<?php

class Bricks_Checkout_Model_Sale_Total_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract {
	public function collect( Mage_Sales_Model_Order_Invoice $invoice ) {
		parent::collect($invoice);

		$amount = $invoice->getOrder()->getCustomStepsDataAmount();
		if ($amount) {
			$invoice->setGrandTotal($amount + $invoice->getGrandTotal());
			$invoice->setBaseGrandTotal($amount + $invoice->getBaseGrandTotal());
		} else {
			$data = $invoice->getOrder()->getCustomStepsData();
			if ( ! empty( $data ) ) {
				$amount = 0;
				foreach( $data as $value ) {
					foreach ( $value as $val )
						$amount += $val['price'];
				}

				$invoice->setGrandTotal(  $amount + $invoice->getGrandTotal() );
				$invoice->setBaseGrandTotal( $amount + $invoice->getBaseGrandTotal() );
			}
		}

		return $this;
	}



}
