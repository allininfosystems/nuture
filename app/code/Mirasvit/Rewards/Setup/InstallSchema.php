<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Setup;

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
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_earning_rule')
        )
        ->addColumn(
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Earning Rule Id')
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name')
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description')
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Is Active')
        ->addColumn(
            'active_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active From')
        ->addColumn(
            'active_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active To')
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type')
        ->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Conditions Serialized')
        ->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Actions Serialized')
        ->addColumn(
            'earning_style',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Earning Style')
        ->addColumn(
            'earn_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Earn Points')
        ->addColumn(
            'monetary_step',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Monetary Step')
        ->addColumn(
            'qty_step',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Qty Step')
        ->addColumn(
            'points_limit',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Points Limit')
        ->addColumn(
            'behavior_trigger',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Behavior Trigger')
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Sort Order')
        ->addColumn(
            'is_stop_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Stop Processing')
        ->addColumn(
            'param1',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Param1')
        ->addColumn(
            'history_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'History Message')
        ->addColumn(
            'email_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Email Message');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_earning_rule_customer_group')
        )
        ->addColumn(
            'earning_rule_customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Earning Rule Customer Group Id')
        ->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group Id')
        ->addColumn(
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Earning Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_customer_group', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_customer_group', ['earning_rule_id']),
                ['earning_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_customer_group',
            'earning_rule_id',
            'mst_rewards_earning_rule',
            'earning_rule_id'
            ),
            'earning_rule_id',
            $installer->getTable('mst_rewards_earning_rule'),
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_earning_rule_product')
        )
        ->addColumn(
            'earning_rule_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Earning Rule Product Id')
        ->addColumn(
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Earning Rule Id')
        ->addColumn(
            'er_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Er Product Id')
        ->addColumn(
            'er_website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Er Website Id')
        ->addColumn(
            'er_customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Er Customer Group Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_product', ['earning_rule_id']),
                ['earning_rule_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_product', ['er_product_id']),
                ['er_product_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_product', ['er_website_id']),
                ['er_website_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_product', ['er_customer_group_id']),
                ['er_customer_group_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_product',
            'er_website_id',
            'store_website',
            'website_id'
            ),
            'er_website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_product',
            'er_product_id',
            'catalog_product_entity',
            'entity_id'
            ),
            'er_product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_product',
            'earning_rule_id',
            'mst_rewards_earning_rule',
            'earning_rule_id'
            ),
            'earning_rule_id',
            $installer->getTable('mst_rewards_earning_rule'),
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_earning_rule_website')
        )
        ->addColumn(
            'earning_rule_website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Earning Rule Website Id')
        ->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website Id')
        ->addColumn(
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Earning Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_website', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_earning_rule_website', ['earning_rule_id']),
                ['earning_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_website',
            'website_id',
            'store_website',
            'website_id'
            ),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_earning_rule_website',
            'earning_rule_id',
            'mst_rewards_earning_rule',
            'earning_rule_id'
            ),
            'earning_rule_id',
            $installer->getTable('mst_rewards_earning_rule'),
            'earning_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_notification_rule')
        )
        ->addColumn(
            'notification_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Notification Rule Id')
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name')
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Is Active')
        ->addColumn(
            'active_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active From')
        ->addColumn(
            'active_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active To')
        ->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Conditions Serialized')
        ->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Actions Serialized')
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Sort Order')
        ->addColumn(
            'is_stop_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Stop Processing')
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type')
        ->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Message');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_notification_rule_customer_group')
        )
        ->addColumn(
            'notification_rule_customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Notification Rule Customer Group Id')
        ->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group Id')
        ->addColumn(
            'notification_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Notification Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_notification_rule_customer_group', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_notification_rule_customer_group', ['notification_rule_id']),
                ['notification_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_notification_rule_customer_group',
            'notification_rule_id',
            'mst_rewards_notification_rule',
            'notification_rule_id'
            ),
            'notification_rule_id',
            $installer->getTable('mst_rewards_notification_rule'),
            'notification_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_notification_rule_website')
        )
        ->addColumn(
            'notification_rule_website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Notification Rule Website Id')
        ->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website Id')
        ->addColumn(
            'notification_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Notification Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_notification_rule_website', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_notification_rule_website', ['notification_rule_id']),
                ['notification_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_notification_rule_website',
            'notification_rule_id',
            'mst_rewards_notification_rule',
            'notification_rule_id'
            ),
            'notification_rule_id',
            $installer->getTable('mst_rewards_notification_rule'),
            'notification_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_notification_rule_website',
            'website_id',
            'store_website',
            'website_id'
            ),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_purchase')
        )
        ->addColumn(
            'purchase_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Purchase Id')
        ->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Quote Id')
        ->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Order Id')
        ->addColumn(
            'spend_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Spend Points')
        ->addColumn(
            'spend_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Spend Amount')
        ->addColumn(
            'spend_min_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Spend Min Points')
        ->addColumn(
            'spend_max_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Spend Max Points')
        ->addColumn(
            'earn_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Earn Points')
            ->addIndex(
                $installer->getIdxName(
                    'mst_rewards_purchase',
                    ['order_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['order_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $installer->getIdxName(
                    'mst_rewards_purchase',
                    ['quote_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['quote_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_referral')
        )
        ->addColumn(
            'referral_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Referral Id')
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Id')
        ->addColumn(
            'new_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'New Customer Id')
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Email')
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name')
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Status')
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id')
        ->addColumn(
            'last_transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Last Transaction Id')
        ->addColumn(
            'points_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Points Amount')
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At')
        ->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Quote Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_referral', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_referral', ['new_customer_id']),
                ['new_customer_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_referral', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_referral', ['last_transaction_id']),
                ['last_transaction_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_referral',
            'last_transaction_id',
            'mst_rewards_transaction',
            'transaction_id'
            ),
            'last_transaction_id',
            $installer->getTable('mst_rewards_transaction'),
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_referral',
            'customer_id',
            'customer_entity',
            'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_referral',
            'new_customer_id',
            'customer_entity',
            'entity_id'
            ),
            'new_customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_referral',
            'store_id',
            'store',
            'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_spending_rule')
        )
        ->addColumn(
            'spending_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Spending Rule Id')
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name')
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description')
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Is Active')
        ->addColumn(
            'active_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active From')
        ->addColumn(
            'active_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active To')
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type')
        ->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Conditions Serialized')
        ->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Actions Serialized')
        ->addColumn(
            'spending_style',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Spending Style')
        ->addColumn(
            'spend_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Spend Points')
        ->addColumn(
            'monetary_step',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Monetary Step')
        ->addColumn(
            'spend_min_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Spend Min Points')
        ->addColumn(
            'spend_max_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Spend Max Points')
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Sort Order')
        ->addColumn(
            'is_stop_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Stop Processing');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_spending_rule_customer_group')
        )
        ->addColumn(
            'spending_rule_customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Spending Rule Customer Group Id')
        ->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group Id')
        ->addColumn(
            'spending_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Spending Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_spending_rule_customer_group', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_spending_rule_customer_group', ['spending_rule_id']),
                ['spending_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_spending_rule_customer_group',
            'spending_rule_id',
            'mst_rewards_spending_rule',
            'spending_rule_id'
            ),
            'spending_rule_id',
            $installer->getTable('mst_rewards_spending_rule'),
            'spending_rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_spending_rule_website')
        )
        ->addColumn(
            'spending_rule_website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Spending Rule Website Id')
        ->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website Id')
        ->addColumn(
            'spending_rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Spending Rule Id')
            ->addIndex(
                $installer->getIdxName('mst_rewards_spending_rule_website', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_rewards_spending_rule_website', ['spending_rule_id']),
                ['spending_rule_id']
            )
            ->addForeignKey(
            $installer->getFkName(
                'mst_rewards_spending_rule_website',
                'spending_rule_id',
                'mst_rewards_spending_rule',
                'spending_rule_id'
            ),
            'spending_rule_id',
            $installer->getTable('mst_rewards_spending_rule'),
                'spending_rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
            $installer->getFkName(
                'mst_rewards_spending_rule_website',
                'website_id',
                'store_website',
                'website_id'
            ),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_rewards_transaction')
        )
        ->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Transaction Id')
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Id')
        ->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Amount')
        ->addColumn(
            'amount_used',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Amount Used')
        ->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Comment')
        ->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code')
        ->addColumn(
            'is_expired',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Expired')
        ->addColumn(
            'is_expiration_email_sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Expiration Email Sent')
        ->addColumn(
            'expires_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Expires At')
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At')
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated At')
            ->addIndex(
                $installer->getIdxName('mst_rewards_transaction', ['customer_id']),
                ['customer_id']
            )
            ->addForeignKey(
            $installer->getFkName(
            'mst_rewards_transaction',
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
    }
}
