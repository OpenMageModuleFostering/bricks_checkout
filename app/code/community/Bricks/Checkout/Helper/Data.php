<?php

class Bricks_Checkout_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Get field base id
	 *
	 * @param string $id
	 * @return string
	 */
	public function getFieldId( $id = '' ) {
		return 'field_' . $id;
	}

	/**
	 * Get field name
	 *
	 * @param string|array $parts
	 * @return string
	 */
	public function getFieldName( $parts = '' ) {
		$name = 'field';

		if ( ! $parts )
			return $name;

		foreach ( ( array ) $parts as $part ) {
			$name .= '[' . $part . ']';
		}

		return $name;
	}
}