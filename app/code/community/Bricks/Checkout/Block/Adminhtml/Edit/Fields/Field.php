<?php

class Bricks_Checkout_Block_Adminhtml_Edit_Fields_Field extends Mage_Adminhtml_Block_Widget {
	const CHECKOUT_FIELDS_PATH = 'global/bricks_checkout/fields';

	protected $_fields = array();

	/**
	 * Get add button ID
	 *
	 * @return string
	 */
	public function getAddButtonId() {
		return $this->getLayout()
			->getBlock('admin.bricks.checkout.steps.fields')
			->getChild('add_button')->getId();
	}

	protected function _prepareLayout() {
		$this->setChild( 'delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData( array(
			'label'	 => Mage::helper( 'bricks_checkout' )->__( 'Delete Option' ),
			'class'	 => 'delete delete-checkout-field'
		) ) );

		foreach ( Mage::getConfig()->getNode( self::CHECKOUT_FIELDS_PATH )->children() as $field ) {
			$this->setChild( $field->getName() . '_field_type', $this->getLayout()->createBlock(
				(string) Mage::getConfig()->getNode( self::CHECKOUT_FIELDS_PATH . '/' . $field->getName() . '/render' )
			) );
		}

		return parent::_prepareLayout();
	}

	public function getDeleteButtonHtml() {
		return $this->getChildHtml('delete_button');
	}

	public function getTypeSelectHtml() {
		/* @var Bricks_Checkout_Helper_Data $helper */
		$helper = Mage::helper('bricks_checkout');

		$select = $this->getLayout()->createBlock('adminhtml/html_select')->setData( array(
			'id'	 => $helper->getFieldId( '{{id}}_type' ),
			'class'	 => 'select select-checkout-field-type required-field-select'
		) )
			->setName( $helper->getFieldName( array( '{{id}}', 'type' ) ) )
			->setOptions( Mage::getSingleton('bricks_checkout/resource_field_type')->toOptionArray() );

		return $select->getHtml();
	}

	public function getRequireSelectHtml() {
		/* @var Bricks_Checkout_Helper_Data $helper */
		$helper = Mage::helper('bricks_checkout');

		$select = $this->getLayout()->createBlock( 'adminhtml/html_select' )->setData( array(
			'id'	 => $helper->getFieldId( '{{id}}_is_require' ),
			'class'	 => 'select'
		) )
			->setName( $helper->getFieldName( array( '{{id}}', 'is_require' ) ) )
			->setOptions( Mage::getSingleton( 'adminhtml/system_config_source_yesno' )->toOptionArray() );

		return $select->getHtml();
	}

	/**
     * Retrieve html templates for different types fields
     *
     * @return string
     */
	public function getTemplatesHtml() {
		$templates = $this->getChildHtml('text_field_type') . "\n" .
			$this->getChildHtml('select_field_type');

		return $templates;
	}

	public function getFieldValues() {
		if ( empty( $this->_fields ) ) {
			$allFields = $this->getStep()->getFields();
			$count = $allFields->getAllIds() ? max($allFields->getAllIds()) + 1 : 1;
			foreach ( $this->getStep()->getFields() as $field ) {
				/** @var Bricks_Checkout_Model_Field $field */
				$_field					 = $field->getData();
				$_field['id']			 = $field->getId();
				$_field['item_count']	 = $count;
				$_field['group']		 = $field->getGroupByType();

				if ( $field->isMultipleType() ) {
					$_field['fieldValues']	 = array();
					$_values				 = $field->getValues();
					$_itemCount				 = max($_values->getAllIds()) + 1;
					foreach ( $_values as $_value ) {
						$value					 = $_value->getData();
						$value['item_count']	 = $_itemCount;
						$_field['fieldValues'][] = $value;
					}
				}

				$this->_fields[] = new Varien_Object( $_field );
			}
		}

		return  $this->_fields;
	}

	public function getPriceValue( $value, $type ) {
		if ( $type == 'percent' ) {
			return number_format( $value, 2, null, '' );
		} elseif ( $type == 'fixed' ) {
			return number_format( $value, 2, null, '' );
		}
	}

	/**
	 *
	 * @return Bricks_Checkout_Model_Step
	 */
	public function getStep() {
		return Mage::registry( 'checkout_step' );
	}

}
