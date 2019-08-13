<?php

class Bricks_Checkout_Model_Sale_Quote extends Mage_Core_Model_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/sale_quote', 'sale_quote_id' );
	}

	public function saveFieldValues( $values ) {
		$values = array_filter( $values );
		foreach ( $values as $field_id => $value ) {
			$field = Mage::getModel('bricks_checkout/field')->load( $field_id );
			/* @var $field Bricks_Checkout_Model_Field */
			if ( $field->isMultipleType() ) {
				$value = is_array( $value ) ? serialize( $value ) : $value;
			} else {
				$value = Mage::helper('core')->escapeHtml( $value );
			}

			/* @var $model Bricks_Checkout_Model_Sale_Quote_Value */
			$model = Mage::getModel('bricks_checkout/sale_quote_value');

			$model->setData( 'sale_quote_id', $this->getId() );
			$model->setData( 'field_id', $field->getId() );
			$model->setData( 'name', $field->getTitle() );
			$model->setData( 'value', $value );
			$model->setData( 'price', $field->getData('price') );
			$model->save();
		}
	}

	public function deleteByQuote( $quote, $step ) {
		$this->getResource()->deleteByQuote( $quote, $step );
	}
}