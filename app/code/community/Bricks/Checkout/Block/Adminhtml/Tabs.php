<?php

class Bricks_Checkout_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct(array $args = array()) {
		parent::__construct($args);
		$this->setId('steps_info_tabs');
		$this->setDestElementId('checkout_step_edit_form');
		$this->setTitle( Mage::helper('bricks_checkout')->__( 'Checkout Step Information' ) );
	}

	protected function _prepareLayout() {
		$step = Mage::registry('checkout_step');
		$this->addTab('default', array(
			'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Details' ),
			'url'	 => $step->getId() ? $this->getUrl( '*/*/edit', array( 'id' => $step->getId() ) ) : $this->getUrl( '*/*/new' ),
			'active' => $this->getRequest()->getParam( 'step' ) ? false : true,
		));

		if ( $step->getId() ) {
			$this->addTab( 'fields', array(
				'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Fields' ),
				'url'	 => $this->getUrl( '*/*/fields', array( 'step' => $step->getId() ) ),
				'active' => $this->getRequest()->getParam( 'step' ) ? true : false
			) );
		}

		parent::_prepareLayout();
	}
}