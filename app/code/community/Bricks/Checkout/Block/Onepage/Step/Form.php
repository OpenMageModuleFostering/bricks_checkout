<?php

class Bricks_Checkout_Block_Onepage_Step_Form extends Mage_Checkout_Block_Onepage_Abstract {
	protected static $_currentField;

	public function _construct() {
		return parent::_construct();
	}

	/**
	 * Get current model
	 *
	 * @return Bricks_Checkout_Model_Steps
	 */
	public function getModel() {
		return Mage::registry('current_checkout_step');
	}

	public function setFormField( $field ) {
		self::$_currentField = $field;
	}

	/**
	 *
	 * @return Bricks_Checkout_Model_Field
	 */
	public function getFormField() {
		return self::$_currentField;
	}

	public function getFormFieldName() {
		return 'field[' . $this->getFormField()->getId() . ']';
	}

	public function getTypeHtml( $type ) {
		$block = $this->getLayout()->createBlock( 'bricks_checkout/onepage_step_form', 'bricks.form.field.' . $type );
		$tmplMaps = array(
			'field'		 => 'bricks/checkout/onepage/form/text.phtml',
			'area'		 => 'bricks/checkout/onepage/form/textarea.phtml',
			'drop_down'	 => 'bricks/checkout/onepage/form/select.phtml',
			'checkbox'	 => 'bricks/checkout/onepage/form/checkbox.phtml',
			'radio'		 => 'bricks/checkout/onepage/form/radio.phtml'
		);
		$block->setTemplate( $tmplMaps[ $type ] );
		return $block->toHtml();
	}

	public function getFieldSavedValue( $default = '' ) {
		$step = Mage::registry('current_checkout_step');
		$quoteData = $this->getQuote()->getCustomStepsData();

		if ( $step && $step->getId() ) {
			if ( ! empty( $quoteData[ $step->getId() ] ) ) {
				$values = $quoteData[ $step->getId() ]['fieldValues'];
				if ( isset( $values[ $this->getFormField()->getId() ] ) ) {
					return $values[ $this->getFormField()->getId() ];
				}
			}
		}

		return $default;
	}
}