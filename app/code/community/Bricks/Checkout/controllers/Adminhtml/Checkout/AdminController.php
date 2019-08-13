<?php

class Bricks_Checkout_Adminhtml_Checkout_AdminController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Steps List Page
	 */
	public function indexAction() {
		$this->_commonSetup()->renderLayout();
	}

	/**
	 * Create new step page
	 */
	public function newAction() {
		$this->_forward('edit');
	}

	/**
	 * Ajax Actions
	 */
	public function gridAction() {
		$this->_commonSetup()->renderLayout();
	}

	/**
	 * Edit Action
	 */
	public function editAction() {
		$id = $this->getRequest()->getParam( 'id' );
		$model = Mage::getModel('bricks_checkout/step');

		if ( $id ) {
			try {
				$model->load( $id );
				Mage::dispatchEvent( 'checkout_step_edit_action', array( 'model' => $model ) );
			} catch (Exception $ex) {
				Mage::logException($ex);
			}
		} else {
			Mage::dispatchEvent( 'checkout_step_new_action' );
		}

		$title = $model->getId() ? $model->getTitle() : Mage::helper('bricks_checkout')->__( 'New Checkout Step' );
		$this->_title( $title );

		Mage::register( 'checkout_step', $model );

		return $this->_commonSetup()
			->_addBreadcrumb( $title, $title )
			->renderLayout();
	}

	public function fieldsAction() {
		$stepId = $this->getRequest()->getParam('step');
		$step = Mage::getModel('bricks_checkout/step');
		try {
			$step->load( $stepId );
		} catch (Exception $ex) {
			Mage::logException($ex);
		}

		if ( $step->getId() ) {
				Mage::register( 'checkout_step', $step );
				$this->_commonSetup()
					->_title( $this->__( 'Checkout Fields' ) )
					->renderLayout();

				return;
		} else {
			$this->_redirect( '*/*/' );
		}
	}

	/**
	 * Save Action
	 */
	public function saveAction() {
		$request = $this->getRequest();

		if ( $request->getPost() ) {
			$model = Mage::getModel('bricks_checkout/step');

			if ( ( $id = $request->getPost( 'step_id' ) ) ) {
				try {
					$model->load( $id );
				} catch( Mage_Core_Exception $ex ) {
					$this->_getSession()->addError( $ex->getMessage() );
					$this->_redirect( '*/*/' );
				} catch( Mage_Exception $ex ) {
					Mage::logException( $ex );
				}
			}

			$model->setData( $request->getPost() );

			try {
				$model->save();
				$this->_getSession()->addSuccess( Mage::helper('bricks_checkout')->__( 'Checkout step updated.' ) );
			} catch ( Mage_Core_Exception $ex ) {
				$this->_getSession()->addError( $ex->getMessage() );
			} catch( Execption $ex ) {
				Mage::logException( $ex );
			}
		}

		$this->_redirect('*/*/edit', array( 'id' => $model->getId() ) );
	}

	/**
	 * Save Fields Action
	 */
	public function savefieldsAction() {
		$step = Mage::getModel('bricks_checkout/step');

		if ( $this->getRequest()->getParam('step') ) {
			try {
				$step->load( $this->getRequest()->getParam('step') );
			} catch (Exception $ex) {
				Mage::logException($ex);
			}
		}

		if ( ! $step->getId() ) {
			$this->_redirect( '*/*/' );
			return;
		}

		$step->saveFields( $this->getRequest()->getPost( 'field' ) );

		$this->_redirect( '*/*/fields', array( 'step' => $step->getId() ) );
	}

	/**
	 * Delete the model
	 */
	public function deleteAction() {
		$id = $this->getRequest()->getParam( 'id' );
		if ( $id ) {
			$model = Mage::getModel('bricks_checkout/step');
			foreach( (array) $id as $_id ) {
				try {
					$model->load( $_id )->delete();
				} catch ( Mage_Core_Exception $ex ) {
					$this->_getSession()->addError( $ex->getMessage() );
				} catch( Exception $ex ) {
					Mage::logException( $ex );
				}
			}
		}

		$this->_redirect( '*/*/' );
	}

	protected function _commonSetup( $handles = null ) {
		/* @var $data Bricks_Checkout_Helper_Data */
		$helper = Mage::helper( 'bricks_checkout' );
		$this->loadLayout( $handles )
			->_setActiveMenu( 'system/custom_checkout' )
			->_title( $helper->__( 'Settings' ) )
			->_title( $helper->__( 'Checkout Steps' ) )
			->_title( $helper->__( 'Settings' ), $helper->__( 'Settings' ) )
			->_title( $helper->__( 'Checkout Steps' ), $helper->__( 'Checkout Steps' ) );

		Mage::dispatchEvent( $this->getFullActionName() . '_before' );

		return $this;
	}
}