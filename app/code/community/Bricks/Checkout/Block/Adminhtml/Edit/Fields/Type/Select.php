<?php

class Bricks_Checkout_Block_Adminhtml_Edit_Fields_Type_Select extends Bricks_Checkout_Block_Adminhtml_Edit_Fields_Type_Abstract {

	public function __construct() {
		parent::__construct();
		$this->setTemplate( 'bricks/checkout/field/select.phtml' );
	}

	protected function _prepareLayout() {
		$this->setChild( 'add_select_row_button', $this->getLayout()->createBlock( 'adminhtml/widget_button' )->setData( array(
			'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Add New Row' ),
			'class'	 => 'add add-select-row',
			'id'	 => 'add_select_row_button_{{field_id}}'
		) ) );

		$this->setChild( 'delete_select_row_button', $this->getLayout()->createBlock( 'adminhtml/widget_button' )->setData( array(
			'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Delete Row' ),
			'class'	 => 'delete delete-select-row icon-btn',
			'id'	 => 'delete_select_row_button'
		) ) );

		return parent::_prepareLayout();
	}

	public function getAddButtonHtml() {
		return $this->getChildHtml( 'add_select_row_button' );
	}

	public function getDeleteButtonHtml() {
		return $this->getChildHtml( 'delete_select_row_button' );
	}
}
