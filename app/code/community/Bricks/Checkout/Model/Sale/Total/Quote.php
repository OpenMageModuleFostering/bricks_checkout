<?php

class Bricks_Checkout_Model_Sale_Total_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	public static $_stepData = null;

	/**
	 * Current step
	 *
	 * @var Bricks_Checkout_Model_Step
	 */
	protected $_step = null;

	protected $_amount = 0.00;

	protected function _getStep() 
	{
		if ( $this->_step ) {
			return $this->_step;
		}

		$stepId = intval( str_replace( 'bricks_checkout_step_', '', $this->getCode() ) );
		$step = Mage::getModel( 'bricks_checkout/step' )->load( $stepId );
		$this->_step = $step;

		return $this->_step;
	}

	public function getLabel() {
		return $this->_getStep()->getName();
	}

	public function collect(Mage_Sales_Model_Quote_Address $address) 
	{
		parent::collect($address);
		if (Mage::app()->getFrontController()->getAction()->getFullActionName() != 'checkout_cart_index') {

			//Mage::log('its here cache enabled 1', null, 'mylogfile.log');
		if ( $address->getAddressType() == $address->getQuote()->getBillingAddress()->getAddressType() )
			return $this;

		$quote = Mage::getSingleton('bricks_checkout/sale_quote')
				->getCollection()
				->getByQuoteByStep( $address->getQuote(), $this->_getStep() );
		$amount = 0.00;
		$values = $this->_getStep()->getValuesForSaleQuote( $quote );
		foreach ( $values->getItems() as $value ) {
			$field = Mage::getModel('bricks_checkout/field')->load( $value->getFieldId() );

			/* @var $field Bricks_Checkout_Model_Field */
			if ( $field->isMultipleType() ) {
				$_value = $value->getData( 'value' );

				if ( 'checkbox' === $field->getType() ) {
					$_value = unserialize( $_value );
					foreach ( array_keys( $_value ) as $value_id ) {
						$price = $field->getPriceForValueId( $value_id );
						if ( $price ) $amount += $price;
					}
				} else {
					$amount += $field->getPriceForValueId( $_value );
				}
			} else {
				$amount += $field->getPrice();
			}
		}

		$this->_amount = $amount;
		$this->_setAmount($amount);
		$this->_setBaseAmount($amount);
//Mage::log('its here cache enabledsss', null, 'mylogfile.log');
		return $this;
	}
	}

	public function fetch( Mage_Sales_Model_Quote_Address $address ) {
		if (Mage::app()->getFrontController()->getAction()->getFullActionName() != 'checkout_cart_index') {
    //Mage::log('its here', null, 'mylogfile.log');

		//echo '<pre>';
		//print_r($address->debug());
		//Mage::log($address->debug(), null, 'mylogfile.log');
		//exit;
		//echo $this->getCode();

		if ( $address->getAddressType() == $address->getQuote()->getBillingAddress()->getAddressType() ) {
			if ( $this->_amount > 0 ) {
				$address->addTotal( array(
					'code'	 => $this->getCode(),
					'title'	 => $this->getLabel(),
					'value'	 => $this->_amount
				) );
			}
		}

		return $this;
		}
	}
}