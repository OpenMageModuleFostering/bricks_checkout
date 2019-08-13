<?php

class Bricks_Checkout_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Initialize
	 */
	public function __construct() {
		parent::__construct();

		$this->setId( 'checkout_step_edit_form' );
		$this->setTitle( Mage::helper('bricks_checkout')->__( 'Checkout Step Information' ) );
	}

	/**
	 * Prepare form
	 *
	 * Add form field and default values
	 *
	 * @return Bricks_Checkout_Block_Admin_Steps_Edit_Form
	 */
	public function _prepareForm() {
		$model = Mage::registry('checkout_step');
		$helper = Mage::helper('bricks_checkout');

		$form = new Varien_Data_Form( array(
			'id'	 => 'edit_form',
			'action' => $this->getUrl( '*/*/save', array( 'id' => $this->getRequest()->getParam( 'id' ) ) ),
			'method' => 'post'
		) );

		$fieldset = $form->addFieldSet( 'base_fieldset', array(
			'legend' => $helper->__( 'Checkout Step Information' )
		) );

		if ( $model->getId() ) {
			$fieldset->addField( 'step_id', 'hidden', array(
				'name' => 'step_id'
			) );
		}

		$fieldset->addField( 'name', 'text', array (
			'name'		 => 'name',
			'label'		 => $helper->__( 'Name' ),
			'title'		 => $helper->__( 'Name' ),
			'required'	 => true
		) );

		$fieldset->addField( 'status', 'select', array (
			'name'		 => 'status',
			'label'		 => $helper->__( 'Status' ),
			'title'		 => $helper->__( 'Status' ),
			'options'	 => array( 'enabled' => 'Enabled', 'disabled' => 'Disabled' )
		) );

		$fieldset->addField( 'sort_order', 'text', array (
			'name'		 => 'sort_order',
			'label'		 => $helper->__( 'Order' ),
			'title'		 => $helper->__( 'Order' ),
			'required'	 => false
		) );

		$fieldset->addField( 'description', 'textarea', array(
			'name'		 => 'description',
			'label'		 => $helper->__( 'Description' ),
			'title'		 => $helper->__( 'Description' ),
			'required'	 => false
		) );

		$form->setValues( $model->getData() );
		$form->setUseContainer( true );
		$this->setForm( $form );

		return parent::_prepareForm();
	}
}