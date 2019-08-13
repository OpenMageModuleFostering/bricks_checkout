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
		$data = $cart->getSalesEntity()->getCustomStepsPaypalItems();
		if (!empty($data)) {
			foreach ($data as $value) {
				$cart->addItem($value['title'], 1, $value['amount']);
			}
		} elseif ($data = $cart->getSalesEntity()->getCustomStepsData()) {
			// Fallback when sales_convert_quote_to_order is not triggered.
			$salesEntity = $cart->getSalesEntity();
			if ($salesEntity instanceof Mage_Sales_Model_Order) {
				foreach ($data as $stepId => $values) {
					$step = Mage::getModel('bricks_checkout/sale_order')->load($stepId);
					$amount = 0.00;
					foreach ($values as $value) {
						$amount += $value['price'];
					}
					$cart->addItem($step->getTitle(), 1, $amount);
				}
			} else {
				foreach ($data as $step) {
					$amount = 0.00;
					foreach ($step['fieldValues'] as $id => $value) {
						$field = Mage::getModel('bricks_checkout/field')->load($id);
						if ($field->isMultipleType()) {
							if ('checkbox' == $field->getType()) {
								$value = unserialize($value);
								foreach (array_keys($value) as $_value_id) {
									$amount += $field->getPriceForValueId($_value_id);
								}
							} else {
								$amount += $field->getPriceForValueId($value);
							}
						} else {
							$amount += $field->getPrice();
						}
					}
					$cart->addItem($step['title'], 1, $amount);
				}
			}
		}
	}

	public function saveOrderBefore($event) {
  //       $salesModel=Mage::getModel("sales/order");
		// $salesCollection = $salesModel->getCollection();
		// foreach($salesCollection as $order)
		// {
		//     $amount= $order->getBaseTotalDue();
		//     $model = Mage::getModel("sales/order")->loadByIncrementId($order->getIncrementId());
		//     // $model->setBaseTotalPaid($amount);
		//     // $model->setTotalPaid($amount);
		//     $model->setTotalPaid($amount);

		//     // echo $model->getBaseTotalPaid()."</br>";;
		//     $model->save(true);
		// }

		// $qoute = $event->getQuote();
		// echo "<pre>";
		// print_r($qoute->debug());exit;
		// exit;
	}

	public function salesConvertQuoteToOrder($event) {
		$quote = $event->getQuote();
		$order = $event->getOrder();

		$steps = $quote->getCustomStepsData();
		if (empty($steps)) {
			return;
		}

		$stepIds = array();
		foreach ($steps as $step) {
			$stepIds[] = $step['step_id'];
		}

		$paypalLineItems = array();
		$amount = 0.00;
		$steps = Mage::getModel('bricks_checkout/step')
			->getCollection()
			->addFieldToFilter('step_id', array('in' => $stepIds));
		foreach ($steps->getItems() as $step) {
			$stepsQuote = Mage::getModel('bricks_checkout/sale_quote')
				->getCollection()
				->getByQuoteByStep($quote, $step);
			$values = $step->getValuesForSaleQuote($stepsQuote);
			$thisAmount = 0.00;
			foreach ($values->getItems() as $value) {
				$field = Mage::getModel('bricks_checkout/field')->load($value->getFieldId());
				/*@ var $field Bricks_Checkout_Model_Field */
				if ($field->isMultipleType()) {
					$_value = $value->getData('value');
					if ('checkbox' == $field->getType()) {
						$_value = unserialize($_value);
						foreach (array_keys($_value) as $_value_id) {
							$price = $field->getPriceForValueId($_value_id);
							if ($price) {
								$thisAmount += $price;
							}
						}
					} else {
						$thisAmount += $field->getPriceForValueId($_value);
					}
				} else {
					$thisAmount += $field->getPrice();
				}
			}
			$amount += $thisAmount;
			$paypalLineItems[] = array(
				'title' => $step->getTitle(),
				'amount' => $thisAmount
			);
		}

		$order->setCustomStepsDataAmount($amount);
		$order->setCustomStepsPaypalItems($paypalLineItems);
	}
}
