<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Bricks_Checkout_OnepageController extends Mage_Checkout_OnepageController {
	protected  $_inStep = false;

	public function saveShippingMethodAction() {
		if ( $this->_expireAjax() )
			return;

		$model = $this->_getCustomCheckoutSteps()->getFirstItem();
		if ( ! $model->getId() ) {
			return parent::saveShippingMethodAction();
		}

		$this->_inStep = true;
		parent::saveShippingMethodAction();

		if ( ! $this->getResponse()->getBody() )
			return;

		$result = array();
		if ( $model ) {
			$result['goto_section'] = 'step-' . $model->getId();
			$result['update_section'] = array(
				'name'	 => 'step-' . $model->getId(),
				'html'	 => $this->_getCustomStepHtml( $model )
			);
		}
		$this->getResponse()->setBody( Mage::helper('core')->jsonEncode( $result ) );
	}

	public function saveCustomStepAction() {
		if ( $this->getRequest()->getPost( 'step' ) ) {
			$steps = $this->_getCustomCheckoutSteps();
			$step = $steps->getItemById( $this->getRequest()->getPost( 'step' ) );
			/* @var $step Bricks_Checkout_Model_Step */
			if ( $step && $step->getId() ) {
				$result = array();
				$errors = $step->validateUserInputs( $this->getRequest()->getPost( 'field' ) );
				if ( ! $errors ) {
					$result['error'] = true;
				} else if ( is_array( $errors ) ) {
					$result['error'] = true;
					$result['message'] = $errors;
				} else {
					$this->getOnepage()->saveCustomStep( $step, $this->getRequest()->getPost( 'field' ) );

					if ( $step->getId() == $steps->getLastItem()->getId() ) {
						$this->_inStep = false;
						$result['goto_section'] = 'payment';
						$result['update_section'] = array(
							'name'	 => 'payment-method',
							'html'	 => $this->_getPaymentMethodsHtml()
						);
					} else {
						$found = false;
						foreach ( $steps->getItems() as $nextStep ) {
							if ( $found ) break;

							if ( $step->getId() == $nextStep->getId() ) {
								$found = true;
							}
						}

						$this->_inStep = true;
						$result['goto_section'] = 'step-' . $nextStep->getId();
						$result['update_section'] = array(
							'name'	 => 'step-' . $nextStep->getId(),
							'html'	 => $this->_getCustomStepHtml( $nextStep )
						);
					}
				}

				$this->getResponse()->setBody( Mage::helper('core')->jsonEncode( $result ) );
			}
		}
	}

	/**
	 * Get step html
	 *
	 * @param Bricks_Checkout_Model_Step $model
	 * @return string
	 */
	protected function _getCustomStepHtml( $model ) {
		if ( ! $this->_inStep )
			return;

		Mage::register('current_checkout_step', $model);
		$layout = $this->getLayout();
		$update= $layout->getUpdate();
		$update->load( 'bricks_checkout_step' );
		$layout->generateXml();
		$layout->generateBlocks();
		return $layout->getOutput();
	}

	protected function _getPaymentMethodsHtml() {
		if ( $this->_inStep ) {
			return;
		}

		return parent::_getPaymentMethodsHtml();
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