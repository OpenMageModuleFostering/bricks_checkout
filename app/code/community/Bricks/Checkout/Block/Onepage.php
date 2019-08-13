<?php

require_once 'Mage/Checkout/Block/Onepage.php';

class Bricks_Checkout_Block_Onepage extends Mage_Checkout_Block_Onepage {
	/**
	 * Get 'one step checkout' steps
	 *
	 * @return array
	 */
	public function getSteps() {
		/* @var $customSteps Bricks_Checkout_Model_Resource_Step_Collection */
		$customSteps = Mage::getSingleton('bricks_checkout/step')->getEnabledStepCollection();

		Mage::register( 'custom_checkout_steps', $customSteps );

		$_steps = parent::getSteps();
		$after = array_slice($_steps, -2 );
		$before = array_slice( $_steps, 0, -2 );
		$steps = array();

		foreach ( Mage::registry('custom_checkout_steps')->getData() as $step ) {
			$steps[ 'step-' . $step['step_id'] ] = array(
				'label'		 => $step['name'],
				'is_show'	 => true
			);
		}

		return array_merge( $before, $steps, $after );
	}

	public function getChild( $name = '' ) {
		if ( strpos( $name, 'step-' ) === 0 ) {
			if ( isset( $this->_children[ $name ] ) ) {
				return $this->_children[ $name ];
			}

			/* @var $customSteps Bricks_Checkout_Model_Resource_Step_Collection */
			$customSteps = Mage::registry('custom_checkout_steps');
			$model = $customSteps->getItemByColumnValue( 'step_id', str_replace( 'step-', '', $name ) );
			$block = $this->getLayout()->createBlock( 'bricks_checkout/onepage_step', 'bricks.custom.checkout.step', array(
				'model'	 => $model,
				'as'	 => 'checkout-step-' . $model->getId()
			) );
			$this->setChild( $name, $block );
			return $block;
		}

		return parent::getChild( $name );
	}
}