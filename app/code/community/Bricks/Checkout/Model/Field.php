<?php

class Bricks_Checkout_Model_Field extends Mage_Core_Model_Abstract {
	/**
	 * Step
	 *
	 * @var Bricks_Checkout_Model_Step
	 */
	protected $_step;

	protected function _construct() {
		$this->_init( 'bricks_checkout/field', 'field_id' );
	}

	public function getStep() {
		if ( ! $this->_step && $this->getStepId() ) {
			$this->setStep( Mage::getModel( 'bricks_checkout/step' )->load( $this->getStepId() ) );
		}

		return $this->_step;
	}

	public function setStep( Bricks_Checkout_Model_Step $step ) {
		$this->_step = $step;
		return $this;
	}

	public function isMultipleType( $type = null ) {
		if ( ! $type )
			$type = $this->getType();

		return (
			$type == 'checkbox'  ||
			$type == 'drop_down' ||
			$type == 'radio'     ||
			$type == 'select'
		);
	}

	public function getGroupByType( $type = null ) {
		if ( ! $type )
			$type = $this->getType();

		switch( $type ) {
			case 'field':
			case 'area':
				return 'text';

			case 'drop_down':
			case 'checkbox':
			case 'radio':
				return 'select';
		}

		return '';
	}

	/**
	 * Get values for this field
	 *
	 * @return Bricks_Checkout_Model_Resource_Field_Value_Collection
	 */
	public function getValues() {
		return Mage::getSingleton('bricks_checkout/field_value')
			->getCollection()
			->addFieldToFilter( 'field_id', $this->getId() );
	}

	/**
	 * Delete values
	 *
	 * @return \Bricks_Checkout_Model_Field
	 */
	public function deleteValues() {
		$this->getResource()->deleteValues( $this );
		return $this;
	}

	protected function _afterSave() {
		if ( $this->isMultipleType() ) {
			foreach ( (array) $this->getData('values') as $value ) {
				$model = Mage::getModel('bricks_checkout/field_value');
				$model->setData( $value );

				if ( $model->getData( 'field_value_id' ) != '-1' ) {
					$model->setId( $model->getData( 'field_value_id' ) );
				} else {
					$model->unsetData( 'field_value_id' );
				}

				if ( $model->getData( 'is_delete' ) == '1' && $model->getId() ) {
					$model->delete();
				} else {
					$model->setData( 'field_id', $this->getId() );
					$model->save();
				}
			}
		}

		return parent::_afterSave();
	}

	/**
	 * Get price for value
	 *
	 * @param integer $value_id
	 * @return real
	 */
	public function getPriceForValueId( $value_id ) {
		$value = Mage::getModel('bricks_checkout/field_value')->load( $value_id );
		if ( $value->getId() ) {
			return $value->getPrice();
		}

		return 0.00;
	}

}