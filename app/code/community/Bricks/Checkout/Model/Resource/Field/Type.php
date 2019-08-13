<?php

class Bricks_Checkout_Model_Resource_Field_Type {
	const CHECKOUT_FIELDS_PATH = 'global/bricks_checkout/fields';

	/**
	 * Get field types array
	 *
	 * @return array
	 */
	public function toOptionArray() {
		$helper = Mage::helper('bricks_checkout');
		$fields = array(
			array( 'value' => '', 'label' => $helper->__( '-- Please Select --' ) )
		);

		foreach ( Mage::getConfig()->getNode( self::CHECKOUT_FIELDS_PATH )->children() as $field ) {
			$types = array();
			$typesPath = self::CHECKOUT_FIELDS_PATH . '/' . $field->getName() . '/types';
			foreach ( Mage::getCOnfig()->getNode( $typesPath )->children() as $type ) {
				$labelPath = self::CHECKOUT_FIELDS_PATH . '/' . $field->getName() . '/types/' . $type->getName() . '/label';
				$types[] = array(
					'label'	 => $helper->__( (string) Mage::getConfig()->getNode( $labelPath ) ),
					'value'	 => $type->getName()
				);
			}

			$labelPath = self::CHECKOUT_FIELDS_PATH . '/' . $field->getName() . '/label';

			$fields[] = array(
				'label'	 => $helper->__( (string) Mage::getConfig()->getNode( $labelPath ) ),
				'value'	 => $types
			);
		}

		return $fields;
	}
}