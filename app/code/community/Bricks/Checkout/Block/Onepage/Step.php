<?php

class Bricks_Checkout_Block_Onepage_Step extends Mage_Checkout_Block_Onepage_Abstract {
	protected function _construct() {
		/* @var $model Bricks_Checkout_Model_Step */
		$model = $this->getModel();
		$data = $model->getData();

		$this->getCheckout()->setStepData( 'step-' . $model->getId(), array(
			'label'	 => $model->getTitle(),
			'isShow' => $this->isShow() && ( count( $data ) > 0 )
		) );

		$this->setTemplate( 'bricks/checkout/onepage.phtml' );

		parent::_construct();
	}

	public function addCheckoutStepsJs() {
		static $once = false;
		if ( ! $once ) {
?>
<script type="text/javascript">
//<![CDATA[
(function(){
	var _init = Checkout.prototype.initialize,
		customSteps = <?php echo Mage::helper('core')->jsonEncode( Mage::registry('custom_checkout_steps')->getColumnValues('step_id') ); ?>,
		totalSteps = customSteps.length;

	Checkout.prototype.initialize = function() {
		var i = 0, steps = [], before, after;
		_init.apply( this, arguments );

		before = this.steps.slice( 0, -2 );
		after = this.steps.slice( -2 );

		for ( ; i < totalSteps; i++ ) {
			steps.push( 'step-' + customSteps[i] );
		}

		this.steps = before.concat( steps.concat( after ) );
	};
})();

var CustomCheckoutStep = Class.create();
CustomCheckoutStep.prototype = {
	initialize: function( step_id, saveUrl ) {
		this.step_id = step_id;
		this.form = $( 'checkout-step-' + this.step_id );
		$(this.form).observe( 'submit', function ( event ) {
			this.save();
			Event.stop( event );
		}.bind( this ) );
		this.saveUrl = saveUrl;
		this.validator = new Validation( this.form );
		this.onSave = this.nextStep.bindAsEventListener( this );
		this.onComplete = this.resetLoadWaiting.bindAsEventListener( this );
	},

	validate: function() {
		if( ! this.validator.validate() ) {
			return false;
		}

		return true;
	},

	save: function() {
		var request;
		if ( checkout.loadWaiting !== false ) {
			return;
		}

		if ( this.validate() ) {
			checkout.setLoadWaiting('step-' + this.step_id);
			request = new Ajax.Request( this.saveUrl, {
				method: 'post',
				onComplete: this.onComplete,
				onSuccess: this.onSave,
				onFailure: checkout.ajaxFailure.bind(checkout),
				parameters: Form.serialize(this.form)
			} );
		}
	},

	resetLoadWaiting: function(transport){
		checkout.setLoadWaiting(false);
		document.body.fire('checkout_step_' + this.step_id, {transport: transport});
	},

	nextStep: function( transport ) {
		var response;

		if (transport && transport.responseText) {
			try {
				response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				response = {};
			}
		}

		if ( response.error ){
			if ( typeof response.message === 'string') {
				alert(response.message);
			} else {
				alert(response.message.join("\n"));
			}

			return false;
		}

		checkout.setStepResponse(response);
	}
};
//]]>
</script>
<?php
			$once = true;
		}
	}
}