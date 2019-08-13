<?php

class Bricks_Checkout_Model_Sale_Order extends Mage_Core_Model_Abstract {
	protected function _construct() {
		$this->_init( 'bricks_checkout/sale_order', 'sale_order_id' );
	}

	public function saveFieldValues( $values ) {
		$values = array_filter( $values );
		foreach ( $values as $field_id => $value ) {
			$field = Mage::getModel('bricks_checkout/field')->load( $field_id );
			/* @var $field Bricks_Checkout_Model_Field */

			if ( $field->isMultipleType() ) {
				if ( 'checkbox' == $field->getType() ) {
					if ( ! is_array($value ) )
						$value = unserialize( $value );

					$option_value = '';
					$price = 0.00;
					foreach ( array_keys( $value ) as $option_value_id ) {
						$optionModel = Mage::getModel('bricks_checkout/field_value')->load( $option_value_id );
						$option_value .= $optionModel->getTitle() . "\n";
						$price += $optionModel->getPrice();
					}
				} else {
					$optionModel = Mage::getModel( 'bricks_checkout/field_value' )->load( $value );
					$option_value = $optionModel->getTitle();
					$price = $optionModel->getPrice();
				}
			} else {
				$option_value = $value;
				$price = $field->getPrice();
			}

			/* @var $model Bricks_Checkout_Model_Sale_Order_Value */
			$model = Mage::getModel('bricks_checkout/sale_order_value');
			$model->setData( array(
				'sale_order_id'	 => $this->getId(),
				'name'			 => $field->getTitle(),
				'value'			 => $option_value,
				'price'			 => $price
			) );
			$model->save();
		}
	}
}