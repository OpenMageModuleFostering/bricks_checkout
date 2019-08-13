<?php

class Bricks_Checkout_Block_Adminhtml_Edit_Fields_Type_Text extends Bricks_Checkout_Block_Adminhtml_Edit_Fields_Type_Abstract {
	public function __construct() {
		parent::__construct();
		$this->setTemplate( 'bricks/checkout/field/text.phtml' );
	}
}