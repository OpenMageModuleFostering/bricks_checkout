<?php

class Bricks_Checkout_Model_Onepage extends Mage_Checkout_Model_Type_Onepage {
	/**
	 * Save step data
	 *
	 * @param Bricks_Checkout_Model_Step
	 * @param array $data
	 * @return \Bricks_Checkout_Model_Onepage
	 */
	public function saveCustomStep( Bricks_Checkout_Model_Step $step, $data ) {
		$allData = $this->getQuote()->getCustomStepsData();
		if ( empty( $allData ) ) {
			$allData = array();
		}

		$allData[ $step->getId() ] = array(
			'step_id'		 => $step->getId(),
			'title'			 => $step->getName(),
			'fieldValues'	 => $data
		);
		$this->getQuote()->setCustomStepsData( $allData );

		$this->getQuote()->collectTotals();
		$this->getQuote()->save();

		$this->getCheckout()
			->setStepData('step-' . $step->getId(), 'allow', true)
			->setStepData('payment', 'allow', true);

		return $this;
	}

	public function saveShippingMethod($shippingMethod) {
		$ret = parent::saveShippingMethod($shippingMethod);
		if ( $this->_getCustomCheckoutSteps()->count() ) {
			$this->getCheckout()->setStepData( 'payment', 'allow', false );
		}

		return $ret;
	}

	/**
	 * Get custom checkout steps
	 *
	 * @return Bricks_Checkout_Model_Resource_Step_Collection
	 */
	protected function _getCustomCheckoutSteps() {
		return Mage::getSingleton('bricks_checkout/step')->getEnabledStepCollection();
	}
}