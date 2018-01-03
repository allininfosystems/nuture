<?php

namespace Bluethink\Productimport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();
		
        $table = $installer->getConnection()->newTable(
            $installer->getTable('productimport_import')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'productimport_import'
        )
        ->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'product_id'
        )
        ->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'product_sku'
        )
        
        ->setComment(
            'Bluethink Productimport productimport_import'
        );
        $installer->endSetup();

    }
}
