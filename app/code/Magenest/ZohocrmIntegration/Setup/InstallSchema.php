<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category  Magenest
 * @package   Magenest_ZohocrmIntegration
 * @author ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema *
 * Create table in database
 *
 * @codeCoverageIgnore
 *
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * Install table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
    
        $installer->startSetup();
        
        /**
         * Create table 'magenest_zohocrm_map'
         */
        $table = $installer->getConnection()
        ->newTable($installer->getTable('magenest_zohocrm_map'))
        ->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Mapping id'
        )
        ->addColumn(
            'zoho_field',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zoho side field'
        )
        ->addColumn(
            'magento_field',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'magento side field'
        )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'magento side field'
        )
        ->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type of mapping Lead or Contact etc'
        )
        ->addColumn(
            'description',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Description'
        )
        ->setComment('Fields mapping between magento and zoho');
        $installer->getConnection()->createTable($table);

        /**
         * Table field to save the magento field ad zoho field which feed the select box of mapping
         * zoho fields will be pulled by using api getFields
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_zohocrm_field')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Field ID'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            30,
            ['nullable' => false],
            'Type'
        )->addColumn(
            'zohocrm',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Zoho Field'
        )->addColumn(
            'magento',
            Table::TYPE_TEXT,
            '30',
            ['nullable' => false],
            'Magento Field'
        )->setComment('Fields to feed mapping selector');

        $installer->getConnection()->createTable($table);

        /**
         * Reporting
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_zohocrm_report')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Field ID'
        )->addColumn(
            'record_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Record Id in ZohoCRM'
        )->addColumn(
            'id_magento',
            Table::TYPE_INTEGER,
            12,
            ['nullable' => true],
            'Id in Magento'
        )->addColumn(
            'action',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true],
            'Action'
        )->addColumn(
            'zohocrm_table',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true],
            'Table of ZohoCRM'
        )->addColumn(
            'username',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Email'
        )->addColumn(
            'datetime',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Date time'
        )->addColumn(
            'status',
            Table::TYPE_INTEGER,
            1,
            ['nullable' => true],
            'Status'
        );
        $installer->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
