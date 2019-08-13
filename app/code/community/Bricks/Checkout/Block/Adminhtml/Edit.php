<?php

class Bricks_Checkout_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Initialize
	 */
	public function __construct() {
		$this->_blockGroup = 'bricks_checkout';
		$this->_controller = 'adminhtml';

		$this->_addButton( 'new', array (
			'label'		 => Mage::helper( 'bricks_checkout' )->__( 'Add New' ),
			'onclick'	 => 'setLocation(\'' . $this->getUrl('*/*/new') . '\')',
			'class'		 => 'add'
		) );

		parent::__construct();
	}

	/**
	 * Header Text
	 *
	 * @return string
	 */
	public function getHeaderText() {
		if ( Mage::registry( 'checkout_step' )->getId() ) {
			return Mage::helper('bricks_checkout')->__( 'Edit Checkout Step' );
		} else {
			return Mage::helper('bricks_checkout')->__( 'Add New Checkout Step' );
		}
	}
}