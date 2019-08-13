<?php

class Bricks_Checkout_Model_Observer {
	protected  static $_savedOnce = false;

	public function saveQuoteAfter( $event ) {
		$quote = $event->getQuote();
		$allData = $quote->getCustomStepsData();
		// print_r($allData);
		if ( ! empty( $allData ) && ! self::$_savedOnce ) {
			foreach ( $allData as $data ) {
				$step = Mage::getModel( 'bricks_checkout/step' )->load( $data['step_id'] );
				if ( $step->getId() ) {
					/* @var $model Bricks_Checkout_Model_Sale_Quote */
					$model = Mage::getModel('bricks_checkout/sale_quote');
					$model->deleteByQuote( $quote, $step );
					$model->setData( $data );
					// print_r($data);
					$model->setData( 'quote_id', $quote->getId() );
					$model->save();
					$model->saveFieldValues( $model->getData( 'fieldValues' ) );
				}
			}
			// exit;
			self::$_savedOnce = true;
		}
	}

	public function loadQuoteAfter( $event ) {
		$quote = $event->getQuote();

		$allData = array();
		$model = Mage::getModel('bricks_checkout/sale_quote')
			->getCollection()
			->addFieldToFilter( 'quote_id', $quote->getId() );

		foreach ( $model->getItems() as $item ) {
			$values = Mage::getModel('bricks_checkout/sale_quote_value')
										->getCollection()
										->addFieldToFilter( 'sale_quote_id', $item->getId() );

			$allData[ $item->getData( 'step_id' ) ] = array (
				'step_id'		 => $item->getData( 'step_id' ),
				'title'			 => $item->getTitle(),
				'fieldValues'	 => array_combine( $values->getColumnValues('field_id'), $values->getColumnValues('value') )
			);
		}

		if ( ! empty( $allData ) ) {
			$quote->setCustomStepsData( $allData );
		}
	}

	public function salesQuoteBeforeTotals( $event ) {
		$allData = $event->getQuote()->getCustomStepsData();
		if ( empty( $allData ) )
			return;

		$config = Mage::getConfig();
		Bricks_Checkout_Model_Sale_Total_Quote::$_stepData = $allData;

		foreach ( $allData as $data ) {
			$step = Mage::getModel( 'bricks_checkout/step' )->load( $data['step_id'] );
			if ( $step->getId() ) {
				$base = 'bricks_checkout_step_' . $step->getId();

				// Sales node
				$baseNode = 'global/sales/quote/totals/' . $base;
				$config->setNode( $baseNode . '/class', 'bricks_checkout/sale_total_quote' );
				$config->setNode( $baseNode . '/after', 'subtotal' );
				$config->setNode( $baseNode . '/before', 'tax' );

				// Sort order node - ??
				$config->setNode( 'default/sales/totals_sort/' . $base, 15 );
			}
		}
	}

	public function saveOrderAfter( $event ) {
		$order = $event->getOrder();
		$quote = $event->getQuote();
		$allData = $quote->getCustomStepsData();
		if ( ! empty( $allData ) ) {
			foreach ( $allData as $data ) {
				$step = Mage::getModel( 'bricks_checkout/step' )->load( $data['step_id'] );
				if ( $step->getId() ) {
					/* @var $model Bricks_Checkout_Model_Sale_Order */
					$model = Mage::getModel('bricks_checkout/sale_order');
					$model->setData( $data );
					$model->setData( 'order_id', $order->getId() );
					$model->save();
					$model->saveFieldValues( $model->getData( 'fieldValues' ) );
				}
			}
		}
	}

	public function loadOrderAfter( $event ) {
		$order = $event->getOrder();
		$steps = Mage::getModel('bricks_checkout/sale_order')
				->getCollection()
				->addFieldToFilter( 'order_id', $order->getId() );

		$data = array();
		foreach ( $steps->getItems() as $step ) {
			$data[ $step->getSaleOrderId() ] = Mage::getModel( 'bricks_checkout/sale_order_value' )
				->getCollection()
				->addFieldToFilter( 'sale_order_id', array( 'eq' => $step->getSaleOrderId() ) )
				->getData();
		}

		$order->setCustomStepsData( $data );
	}

	public function updatePaypalTotal( $event ) {
		$cart = $event->getPaypalCart();
		$data = $cart->getSalesEntity()->getCustomStepsData();
		if ( ! empty( $data ) ) {
			$amount = 0;
			foreach( $data as $value ) {
				foreach ( $value as $val )
					$amount += $val['price'];
			}
			$cart->updateTotal( Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL, $amount );
		}
	}

	public function automaticallyInvoiceShipCompleteOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
 
        $orders = Mage::getModel('sales/order_invoice')->getCollection()
                        ->addAttributeToFilter('order_id', array('eq'=>$order->getId()));
        $orders->getSelect()->limit(1);  
 
        if ((int)$orders->count() !== 0) {
            return $this;
        }
 
        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
 
            try {
                if(!$order->canInvoice()) {
                    $order->addStatusHistoryComment('Inchoo_Invoicer: Order cannot be invoiced.', false);
                    $order->save();  
                }
 
                //START Handle Invoice
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
 
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                $invoice->register();
 
                $invoice->getOrder()->setCustomerNoteNotify(false);          
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment('Automatically INVOICED by Inchoo_Invoicer.', false);
 
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
 
                $transactionSave->save();
                //END Handle Invoice
 
                //START Handle Shipment
                $shipment = $order->prepareShipment();
                $shipment->register();
 
                $order->setIsInProcess(true);
                $order->addStatusHistoryComment('Automatically SHIPPED by Inchoo_Invoicer.', false);
 
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                //END Handle Shipment
            } catch (Exception $e) {
                $order->addStatusHistoryComment('Inchoo_Invoicer: Exception occurred during automaticallyInvoiceShipCompleteOrder action. Exception message: '.$e->getMessage(), false);
                $order->save();
            }                
        }
 
		return $this;        
    }


}
