<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Setup;

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
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('twlogin_customer'));
        $installer->getConnection()->dropTable($installer->getTable('authorlogin_customer'));
        $installer->getConnection()->dropTable($installer->getTable('vklogin_customer'));

        /*
                     * Create table ​twlogin_customer
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('twlogin_customer')
        )->addColumn(
            'twitter_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'primary key comment'
        )->addColumn(
            'twitter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Twitter Id'
        )->addColumn(
            'instagram_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Instagram Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Customer Id'
        )->addIndex(
            $installer->getIdxName('twlogin_customer', 'customer_id'),
            'customer_id'
        )->addForeignKey(
            $installer->getFkName(
                'twlogin_customer',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        /*
                     * End create table twlogin_customer
        */
        /**
         * Create table authorlogin_customer
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('authorlogin_customer')
        )->addColumn(
            'author_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'primary key comment'
        )->addColumn(
            'author_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Author Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Customer Id'
        )->addIndex(
            $installer->getIdxName('authorlogin_customer', 'customer_id'),
            'customer_id'
        )->addForeignKey(
            $installer->getFkName(
                'authorlogin_customer',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        /*
                     * End create table authorlogin_customer
        */
        /**
         * Create table vklogin_customer
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('vklogin_customer')
        )->addColumn(
            'vk_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'primary key comment'
        )->addColumn(
            'vk_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Vk Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Customer Id'
        )->addIndex(
            $installer->getIdxName('vklogin_customer', 'customer_id'),
            'customer_id'
        )->addForeignKey(
            $installer->getFkName(
                'vklogin_customer',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        /*
                     * End create table authorlogin_customer
        */
        $installer->endSetup();
    }
}
