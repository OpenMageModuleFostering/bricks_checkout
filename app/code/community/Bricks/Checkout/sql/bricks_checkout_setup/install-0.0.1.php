<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

$steps = $adapter->newTable( $installer->getTable( 'bricks_checkout/step' ) );
$steps
	->addColumn( 'step_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )->addColumn( 'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => false
	) )->addColumn( 'description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable' => false
	) )->addColumn( 'sort_order', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'default'	 => 0,
		'nullable'	 => false
	) )->addColumn( 'status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'default'	 => 'enabled',
		'nullable'	 => true
	) )
	;
$adapter->createTable($steps );

$field = $adapter->newTable( $installer->getTable( 'bricks_checkout/field' ) );
$field
	->addColumn( 'field_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true,
	) )
	->addColumn( 'step_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false,
		'default'	 => '0',
	) )
	->addColumn( 'type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
		'nullable'	 => true,
		'default'	 => null,
	) )
	->addColumn( 'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addColumn( 'price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable'	 => false,
		'default'	 => '0.0000'
	) )
	->addColumn( 'is_require', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'	 => false,
		'default'	 => '0',
	) )
	->addColumn( 'sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'	 => true,
		'nullable'	 => false,
		'default'	 => '0',
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/field', array( 'step_id' ) ), array( 'step_id' ) )
	->addForeignKey(
		$installer->getFkName( 'bricks_checkout/field', 'step_id', 'bricks_checkout/step', 'step_id' ),
		'step_id', $installer->getTable('bricks_checkout/step'), 'step_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	;
$adapter->createTable( $field );

$field_values = $adapter->newTable( $installer->getTable( 'bricks_checkout/field_value' ) );
$field_values
	->addColumn( 'field_value_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )
	->addColumn( 'field_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false,
		'default'	 => '0',
	) )
	->addColumn( 'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addColumn( 'price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable'	 => false,
		'default'	 => '0.0000'
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/field_value', array( 'field_id' ) ), array( 'field_id' ) )
	->addForeignKey(
		$installer->getFkName( 'bricks_checkout/field_value', 'field_id', 'bricks_checkout/field', 'field_id' ),
		'field_id', $installer->getTable('bricks_checkout/field'), 'field_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	;
$adapter->createTable( $field_values );

$sale_quote = $adapter->newTable( $installer->getTable( 'bricks_checkout/sale_quote' ) );
$sale_quote
	->addColumn( 'sale_quote_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )
	->addColumn( 'quote_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false
	) )
	->addColumn( 'step_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => true
	) )
	->addColumn( 'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/sale_quote', array( 'quote_id' ) ), array( 'quote_id' ) )
	;
$adapter->createTable( $sale_quote );

$sale_quote_value = $adapter->newTable( $installer->getTable( 'bricks_checkout/sale_quote_value' ) );
$sale_quote_value
	->addColumn( 'sale_quote_value_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )
	->addColumn( 'sale_quote_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false
	) )
	->addColumn( 'field_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => true
	) )
	->addColumn( 'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addColumn( 'value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable' => false
	) )
	->addColumn( 'price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable'	 => false,
		'default'	 => '0.0000'
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/sale_quote_value', array( 'sale_quote_id' ) ), array( 'sale_quote_id' ) )
	->addForeignKey(
		$installer->getFkName( 'bricks_checkout/sale_quote_value', 'sale_quote_id', 'bricks_checkout/sale_quote', 'sale_quote_id' ),
		'sale_quote_id', $installer->getTable( 'bricks_checkout/sale_quote' ), 'sale_quote_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	;
$adapter->createTable( $sale_quote_value );

$sale_order = $adapter->newTable( $installer->getTable( 'bricks_checkout/sale_order' ) );
$sale_order
	->addColumn( 'sale_order_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )
	->addColumn( 'order_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false
	) )
	->addColumn( 'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/sale_order', array( 'order_id' ) ), array( 'order_id' ) )
	;
$adapter->createTable( $sale_order );

$sale_order_value = $adapter->newTable( $installer->getTable( 'bricks_checkout/sale_order_value' ) );
$sale_order_value
	->addColumn( 'sale_order_value_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'identity'	 => true,
		'unsigned'	 => true,
		'nullable'	 => false,
		'primary'	 => true
	) )
	->addColumn( 'sale_order_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
		'unsigned'	 => true,
		'nullable'	 => false
	) )
	->addColumn( 'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable'	 => true,
		'default'	 => null
	) )
	->addColumn( 'value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable' => false
	) )
	->addColumn( 'price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable'	 => false,
		'default'	 => '0.0000'
	) )
	->addIndex( $installer->getIdxName( 'bricks_checkout/sale_order_value', array( 'sale_order_id' ) ), array( 'sale_order_id' ) )
	->addForeignKey(
		$installer->getFkName( 'bricks_checkout/sale_order_value', 'sale_order_id', 'bricks_checkout/sale_order', 'sale_order_id' ),
		'sale_order_id', $installer->getTable( 'bricks_checkout/sale_order' ), 'sale_order_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	;
$adapter->createTable( $sale_order_value );

$installer->endSetup();