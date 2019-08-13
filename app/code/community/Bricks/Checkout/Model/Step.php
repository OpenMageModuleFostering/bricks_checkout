<?php

class Bricks_Checkout_Model_Step extends Mage_Core_Model_Abstract {

	protected function _construct() {
		Mage::app()->cleanCache();
		$this->_init( 'bricks_checkout/step', 'step_id' );
	}

	/**
	 * Get fields for step
	 *
	 * @param Bricks_Checkout_Model_Step $step
	 * @return Bricks_Checkout_Model_Resource_Field_Collection
	 */
	public function getFields() {
		return Mage::getSingleton('bricks_checkout/field')
			->getCollection()
			->addFieldToFilter( 'step_id', $this->getId() )
			->setOrder( 'step_id', Bricks_Checkout_Model_Resource_Step_Collection::SORT_ORDER_DESC )
			->setOrder( 'sort_order', Bricks_Checkout_Model_Resource_Step_Collection::SORT_ORDER_ASC );
	}

	/**
	 * Save fields for step
	 *
	 * @param array $fields
	 * @return Bricks_Checkout_Model_Step
	 */
	public function saveFields( array $fields ) {
		foreach ( $fields as $field ) {
			/* @var Bricks_Checkout_Model_Field $model */
			$model = Mage::getModel( 'bricks_checkout/field' );
			$model->setData( $field );

			if ( $model->getData( 'field_id' ) ) {
				$model->setId( $model->getData( 'field_id' ) );
			} else {
				$model->unsetData( 'field_id' );
			}

			$isEdit = ( bool ) $model->getId();

			if ( $model->getData( 'is_delete' ) == '1' && $isEdit ) {
				$model->deleteValues();
				$model->delete();
			} else {
				$prevGroup = $model->getGroupByType( $model->getData('previous_type') );
				if ( $isEdit && $prevGroup && $prevGroup !== $model->getGroupByType() && $model->isMultipleType( $prevGroup ) ) {
					$model->deleteValues();
				}

				$model->setData( 'step_id', $this->getId() );
				$model->save();
			}
		}

		return $this;
	}

	/**
	 * Get saved values for quote
	 *
	 * @param Bricks_Checkout_Model_Sale_Quote $quote
	 * @return Bricks_Checkout_Model_Resource_Sale_Quote_Value_Collection
	 */
	public function getValuesForSaleQuote( $quote ) {
		return Mage::getModel('bricks_checkout/sale_quote_value')
			->getCollection()
			->addFieldToFilter( 'sale_quote_id', $quote->getId() )
			->addFieldToFilter( 'field_id', array( 'in' => $this->getFields() ->getAllIds() ) );
	}

	/**
	 * Get saved values for order
	 *
	 * @param Bricks_Checkout_Model_Sale_Order $quote
	 * @return Bricks_Checkout_Model_Resource_Sale_Order_Value_Collection
	 */
	public function getValuesForSaleOrder( $order ) {
		return Mage::getModel('bricks_checkout/sale_order_value')
			->getCollection()
			->addFieldToFilter( 'sale_order_id', $order->getId() )
			->addFieldToFilter( 'field_id', array( 'in' => $this->getFields() ->getAllIds() ) );
	}

	public function validateUserInputs( $inputs = null ) {
		$fields = $this->getFields();
		// Always pass for empty steps
		if ( ! $fields->count() )
			return true;

		if ( empty( $inputs ) )
			return false;

		$errors = array();

		foreach ( $fields->getItems() as $field ) {
			/* @var $field Bricks_Checkout_Model_Field */
			if ( $field->getIsRequire() && ( ! isset( $inputs[ $field->getId() ] ) || empty( $inputs[ $field->getId() ] ) ) ) {
				$errors[] = sprintf( Mage::helper('bricks_checkout')->__( '%s is a required field' ), $field->getTitle() );
				continue;
			}

			if ( $field->isMultipleType() ) {
				$values = $inputs[ $field->getId() ];
				if ( is_array( $values ) ) {
					// Checkboxes
					$values = array_keys( $values );
				}

				$validValues = $field->getValues();
				foreach ( ( array ) $values as $value ) {
					if ( ! $validValues->getItemById( $value ) ) {
						$errors[] = sprintf( Mage::helper( 'bricks_checkout' )->__( 'Invalid value chosen for %s' ), $field->getTitle() );
					}
				}
			}
		}

		if ( empty( $errors ) )
			return true;

		return $errors;
	}

	/**
	 * Get enabled steps by sort order
	 *
	 * @return Bricks_Checkout_Model_Resrouce_Step_Collection
	 */
	public function getEnabledStepCollection() {
		return $this->getCollection()
			->addFieldToFilter( 'status', array( 'eq' => 'enabled' ) )
			->setOrder( 'sort_order', Bricks_Checkout_Model_Resource_Step_Collection::SORT_ORDER_ASC );
	}

}
