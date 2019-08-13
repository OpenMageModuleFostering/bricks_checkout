<?php

class Bricks_Checkout_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
     * Constructor
     * Set main configuration of grid
     */
    public function __construct() {
        parent::__construct();
        $this->setId('checkoutSteps');
		$this->setDefaultSort('step_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);
    }

	/**
     * Prepare collection for grid
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
	protected function _prepareCollection() {
		/* @var $collection Bricks_Checkout_Model_Resource_Steps_Collection */
		$collection = Mage::getModel('bricks_checkout/step')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
     * Prepare grid columns
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
	protected function _prepareColumns() {
		$helper = Mage::helper('bricks_checkout');

		$this
			->addColumn( 'step_id', array(
				'header'	 => $helper->__( 'id' ),
				'width'		 => '50px',
				'index'		 => 'step_id',
				'sortable'	 => true
			) )
			->addColumn( 'name', array(
				'header'	 => $helper->__( 'name' ),
				'index'		 => 'name',
				'sortable'	 => true
			) )
			->addColumn( 'order', array(
				'header'	 => $helper->__( 'Order' ),
				'index'		 => 'sort_order',
				'width'		 => '50px',
				'sortable'	 => true
			) )
			->addColumn( 'description', array(
				'header'	 => $helper->__( 'Description' ),
				'index'		 => 'description',
				'sortable'	 => false
			) )
			->addColumn( 'status', array(
				'header'	 => $helper->__( 'Status' ),
				'index'		 => 'status',
				'sortable'	 => false,
				'width'		 => '70px',
				'type'		 => 'options',
				'options'	 => array( 'enabled' => $helper->__( 'Enabled' ), 'disabled' => $helper->__( 'Disabled' ) )
			) )
			->addColumn( 'action', array(
				'header'	 => $helper->__( 'Action' ),
				'width'		 => '100px',
				'type'		 => 'action',
				'getter'	 => 'getId',
				'actions'	 => array(
					array(
						'caption'	 => $helper->__( 'Edit' ),
						'url'		 => array( 'base' => '*/*/edit' ),
						'field'		 => 'id'
					)
				),
				'filter'	 => false,
				'sortable'	 => false
			) );

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField( 'id' );
		$this->getMassactionBlock()->setFormFieldName( 'id' );
		$this->getMassactionBlock()->addItem( 'delete', array(
			'label'		 => Mage::helper( 'bricks_checkout' )->__( 'Delete' ),
			'url'		 => $this->getUrl( '*/*/delete', array( '' => '' ) ),
			'confirm'	 => Mage::helper( 'bricks_checkout' )->__( 'Are you sure' )
		) );

		return parent::_prepareMassaction();
	}

	/**
     * Return item view URL
     * @return string
     */
	public function getRowUrl($item) {
        return $this->getUrl('*/*/edit', array('id' => $item->getId()));
    }

	/**
	 * Return Grid URL
	 *
	 * @return string
	 */
	public function getGridUrl() {
        return $this->getUrl( '*/*/grid', array( '_current' => true ) );
	}
}