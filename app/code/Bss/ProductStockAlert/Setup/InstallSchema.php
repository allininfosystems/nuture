<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductStockAlert\Setup;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        
        /**
         * Create table 'bss_product_alert_stock'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_product_alert_stock')
        )->addColumn(
            'alert_stock_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product alert stock id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer id'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Email'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Name'
        )->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Product Sku'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Product id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Website id'
        )->addColumn(
            'add_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Product alert add date'
        )->addColumn(
            'send_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Product alert send date'
        )->addColumn(
            'send_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Send Count'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Product alert status'
        )->addIndex(
            $installer->getIdxName(
                'bss_product_alert_stock',
                ['customer_email', 'website_id', 'product_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['customer_email', 'website_id', 'product_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('bss_product_alert_stock', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('bss_product_alert_stock', ['product_id']),
            ['product_id']
        )->addIndex(
            $installer->getIdxName('bss_product_alert_stock', ['website_id']),
            ['website_id']
        )->setComment(
            'Product Alert Stock'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
