<?php
/**
 * Copyright © 2015 Bluethink. All rights reserved.
 */

namespace Bluethink\Faq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'faq_faq'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('faq_faq')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'faq_faq'
        )
		->addColumn(
            'questions',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'questions'
        )
		->addColumn(
            'answers',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'answers'
        )
		->addColumn(
            'assigned_category',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'assigned_category'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Bluethink Faq faq_faq'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
