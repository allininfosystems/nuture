<?php
/**
 * Webinse
 *
 * PHP Version 5.6.23
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */
/**
 * Install script for Barcode.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table webinse_barcode_generated_barcodes
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('webinse_barcode_generated_barcodes')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Name'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'SKU'
        )->addColumn(
            'barcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode'
        )->addColumn(
            'barcode_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode Type'
        )->addColumn(
            'image_format',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode image format'
        )->addColumn(
            'encoded_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Encoded Image'
        )->setComment(
            'List of generated barcodes'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table webinse_barcode_history_barcodes
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('webinse_barcode_history_barcodes')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Name'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'SKU'
        )->addColumn(
            'barcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode'
        )->addColumn(
            'barcode_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode Type'
        )->addColumn(
            'image_format',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Barcode image format'
        )->addColumn(
            'encoded_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Encoded Image'
        )->addColumn(
            'username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Username'
        )->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Creation Date'
        )->setComment(
            'History of created barcodes'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}