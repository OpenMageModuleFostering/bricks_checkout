<?php

class Bricks_Checkout_Block_Adminhtml_Steps extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'bricks_checkout';
		$this->_controller = 'adminhtml';
		$this->_headerText = Mage::helper('bricks_checkout')->__( 'Checkout Steps' );
		parent::__construct();
	}
}