<?php

namespace Cminds\StockNotification\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /**
         * Create table 'cminds_stocknotification_request'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('cminds_stocknotification_request'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Stock Notification Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Id has foreign key entity_id from table catalog_product_entity'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true

                ],
                'Stock Notification Customer Id'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Customer E-mail Address'
            )
            ->addColumn(
                'notified',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default'  => false

                ],
                'Is E-mail notification has been send'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Created At Date'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Last Modified Date'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'cminds_stocknotification_request',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addIndex(
                $setup->getIdxName(
                    'cminds_stocknotification_request',
                    ['id', 'product_id', 'email'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['id', 'product_id', 'email'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName(
                    'cminds_stocknotification_request',
                    ['notified']
                ),
                ['notified']
            )
            ->setComment('Stock Notification table');

        $setup->getConnection()->createTable($table);
    }
}
