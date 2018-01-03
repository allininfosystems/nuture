<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('rewardpoints_customer'));
        $installer->getConnection()->dropTable($installer->getTable('rewardpoints_rate'));
        $installer->getConnection()->dropTable($installer->getTable('rewardpoints_transaction'));
        /**
         * Create table 'rewardpoints_customer'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpoints_customer'))
            ->addColumn(
                'reward_id',Table::TYPE_INTEGER,NULL,
                ['identity' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true]
            )
            ->addColumn(
                'customer_id',Table::TYPE_INTEGER,NULL,
                ['nullable' => false]
            )

            ->addColumn(
                'point_balance',Table::TYPE_INTEGER,NULL,
                ['nullable' => false, 'default'=>0]
            )
            ->addColumn(
                'holding_balance',Table::TYPE_INTEGER,NULL,
                ['nullable' => false, 'default'=>0]
            )
            ->addColumn(
                'spent_balance',Table::TYPE_INTEGER,NULL,
                ['nullable' => false, 'default'=>0]
            )
            ->addColumn(
                'is_notification',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'expire_notification',Table::TYPE_INTEGER,NULL,
                ['nullable' => false]
            );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'rewardpoints_rate'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpoints_rate'))
            ->addColumn(
                'rate_id',Table::TYPE_INTEGER,NULL,
                ['identity' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true]
            )
            ->addColumn(
                'website_ids',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false,'unsigned' => true]
            )
            ->addColumn(
                'customer_group_ids',Table::TYPE_TEXT,NULL
            )
            ->addColumn(
                'direction',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'points',Table::TYPE_INTEGER,NULL,
                ['nullable' => false,'default' => 0]
            )
            ->addColumn(
                'money',Table::TYPE_DECIMAL,NULL,
                ['nullable' => false,'default' => 0]
            )
            ->addColumn(
                'max_price_spended_type',Table::TYPE_TEXT,NULL
            )
            ->addColumn(
                'max_price_spended_value',Table::TYPE_DECIMAL,NULL
            )
            ->addColumn(
                'status',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false,'default' => 0]
            )
            ->addColumn(
                'sort_order',Table::TYPE_INTEGER,NULL,
                ['nullable' => false]
            )->addForeignKey(
                $installer->getFkName('rewardpoints_rate', 'website_ids', 'store_website', 'website_id'),
                'website_ids',
                $installer->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        /**
         * Create  'rewardpoints_transaction' table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpoints_transaction'))
            ->addColumn(
                'transaction_id',Table::TYPE_INTEGER,NULL,
                ['identity' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true]
            )
            ->addColumn(
                'reward_id',Table::TYPE_INTEGER,NULL
            )
            ->addColumn(
                'customer_id',Table::TYPE_INTEGER,NULL
            )
            ->addColumn(
                'customer_email',Table::TYPE_TEXT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'title',Table::TYPE_TEXT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'action',Table::TYPE_TEXT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'action_type',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false,'default'=>0]
            )
            ->addColumn(
                'store_id',Table::TYPE_SMALLINT,NULL
            )
            ->addColumn(
                'point_amount',Table::TYPE_INTEGER,NULL,
                ['nullable' => false,'default'=>0]
            )
            ->addColumn(
                'point_used',Table::TYPE_INTEGER,NULL,
                ['nullable' => false,'default'=>0]
            )
            ->addColumn(
                'real_point',Table::TYPE_INTEGER,NULL,
                ['nullable' => false,'default'=>0]
            )
            ->addColumn(
                'status',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false]
            )
            ->addColumn(
                'created_time',Table::TYPE_DATETIME,NULL
            )
            ->addColumn(
                'updated_time',Table::TYPE_DATETIME,NULL
            )
            ->addColumn(
                'expiration_date',Table::TYPE_DATETIME,NULL
            )
            ->addColumn(
                'expire_email',Table::TYPE_SMALLINT,NULL,
                ['nullable' => false,'default'=>0]
            )
            ->addColumn(
                'order_id',Table::TYPE_INTEGER,NULL
            )
            ->addColumn(
                'order_increment_id',Table::TYPE_TEXT,NULL
            )
            ->addColumn(
                'order_base_amount',Table::TYPE_DECIMAL,NULL
            )
            ->addColumn(
                'order_amount',Table::TYPE_DECIMAL,NULL
            )
            ->addColumn(
                'base_discount',Table::TYPE_DECIMAL,NULL
            )
            ->addColumn(
                'discount',Table::TYPE_DECIMAL,NULL
            )
            ->addColumn(
                'extra_content',Table::TYPE_TEXT,NULL
            );
        $installer->getConnection()->createTable($table);

        /**
         * Add more column to table  'quote_item'
         */

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'rewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'quote_address'
         */

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'rewardpoints_base_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_order'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_spent',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_base_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_order_item'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'rewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'rewardpoints_spent',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'rewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'rewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_invoice'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'rewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'rewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'rewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_creditmemo'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'rewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'rewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'rewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * create rewardpoints_rule table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpoints_rule'))
            ->addColumn(
                'max_price_spended_type',Table::TYPE_TEXT,NULL
            )
            ->addColumn(
                'max_price_spended_value',Table::TYPE_DECIMAL,NULL
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

}
