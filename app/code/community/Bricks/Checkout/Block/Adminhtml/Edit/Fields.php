<?php

class Bricks_Checkout_Block_Adminhtml_Edit_Fields extends Mage_Adminhtml_Block_Widget {
	protected function _construct() {
		parent::_construct();
	}

	/**
	 * Get current checkout step
	 *
	 * @return Bricks_Checkout_Model_Step
	 */
	public function getStep() {
		return Mage::registry('checkout_step');
	}

	public function getFields() {
		return $this->getSteps()->getFields();
	}

	protected function _prepareLayout() {
		$this->setChild( 'add_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData( array(
			'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Add New Field' ),
			'class'	 => 'add',
			'id'	 => 'add_new_field'
		) ) );

		$this->setChild( 'save_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData( array(
			'label'		 => Mage::helper( 'bricks_checkout' )->__( 'Save' ),
			'class'		 => 'save',
			'onclick'	 => 'editForm.submit()'
		) ) );

		return parent::_prepareLayout();
	}

	public function getAddButtonHtml() {
		return $this->getChildHtml('add_button');
	}

	public function getSaveButtonHtml() {
		return $this->getChildHtml('save_button');
	}

	public function getOptionsBoxHtml() {
		return $this->getChildChildHtml('options_box');
	}

	public function getSaveUrl() {
		return $this->getUrl( '*/*/savefields', array( 'step' => $this->getStep()->getId() ) );
	}
}