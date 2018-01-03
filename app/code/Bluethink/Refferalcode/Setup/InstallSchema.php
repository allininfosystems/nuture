<?php

namespace Bluethink\Refferalcode\Setup;

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
            $installer->getTable('custom_refferalcode')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'custom_refferalcode'
        )
        ->addColumn(
            'refferalcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'refferalcode'
        )
        ->addColumn(
            'rewardpoint',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'rewardpoint'
        )
        ->setComment(
            'Bluethink Refferalcode  custom_refferalcode'
        );
        
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

    }
}
