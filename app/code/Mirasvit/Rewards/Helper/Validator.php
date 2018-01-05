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



namespace Mirasvit\Rewards\Helper;

class Validator //extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Helper\Validator    $validatorCrc
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Validator $validatorCrc,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->validatorCrc = $validatorCrc;
        $this->context = $context;
        //        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function testMirasvitCrc()
    {
        $modules = ['Rewards'];

        return $this->validatorCrc->testMirasvitCrc($modules);
    }

    /**
     * @return array
     */
    public function testISpeedCache()
    {
        $result = self::SUCCESS;
        $title = 'My_Ispeed';
        $description = [];
        if ($this->context->getModuleManager()->isEnabled('My_Ispeed')) {
            $result = self::INFO;
            $description[] = 'Extension My_Ispeed is installed. Please, go to the Configuration > Settings > I-Speed '.
                '> General Configuration and add \'rewards\' to the list of Ignored URLs. Then clear ALL cache.';
        }

        return [$result, $title, $description];
    }

    /**
     * @return array
     */
    public function testMgtVarnishCache()
    {
        $result = self::SUCCESS;
        $title = 'Mgt_Varnish';
        $description = [];
        if ($this->context->getModuleManager()->isEnabled('Mgt_Varnish')) {
            $result = self::INFO;
            $description[] = 'Extension Mgt_Varnish is installed. Please, go to the Configuration > Settings > '.
                'MGT-COMMERCE.COM > Varnish and add \'rewards\' to the list of Excluded Routes. Then clear ALL cache.';
        }

        return [$result, $title, $description];
    }

    /**
     * Performs structural test of database with types check.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testTableStructure()
    {
        $structure = [
            'customer/entity' => [],
            'core/store' => [],
            'rewards/earning_rule' => [
                'earning_rule_id' => 'int(11)',
                'name' => 'varchar(255)',
                'description' => 'text',
                'is_active' => 'int(11)',
                'active_from' => 'timestamp',
                'active_to' => 'timestamp',
                'type' => 'varchar(255)',
                'conditions_serialized' => 'text',
                'actions_serialized' => 'text',
                'earning_style' => 'varchar(255)',
                'earn_points' => 'int(11)',
                'monetary_step' => 'float',
                'qty_step' => 'int(11)',
                'points_limit' => 'int(11)',
                'behavior_trigger' => 'varchar(255)',
                'sort_order' => 'int(11)',
                'is_stop_processing' => 'tinyint(1)',
                'param1' => 'varchar(255)',
                'history_message' => 'text',
                'email_message' => 'text',
            ],
            'rewards/earning_rule_customer_group' => [
                'earning_rule_customer_group_id' => 'int(11)',
                'customer_group_id' => 'smallint(5) unsigned',
                'earning_rule_id' => 'int(11)',
            ],
            'rewards/earning_rule_product' => [
                'earning_rule_product_id' => 'int(11)',
                'earning_rule_id' => 'int(11)',
                'er_product_id' => 'int(10) unsigned',
                'er_website_id' => 'smallint(5) unsigned',
                'er_customer_group_id' => 'smallint(5) unsigned',
            ],
            'rewards/earning_rule_website' => [
                'earning_rule_website_id' => 'int(11)',
                'website_id' => 'smallint(5) unsigned',
                'earning_rule_id' => 'int(11)',
            ],
            'rewards/notification_rule' => [
                'notification_rule_id' => 'int(11)',
                'name' => 'varchar(255)',
                'is_active' => 'int(11)',
                'active_from' => 'timestamp',
                'active_to' => 'timestamp',
                'conditions_serialized' => 'text',
                'actions_serialized' => 'text',
                'sort_order' => 'int(11)',
                'is_stop_processing' => 'tinyint(1)',
                'type' => 'varchar(255)',
                'message' => 'text',
            ],
            'rewards/notification_rule_customer_group' => [
                'notification_rule_customer_group_id' => 'int(11)',
                'customer_group_id' => 'smallint(5) unsigned',
                'notification_rule_id' => 'int(11)',
            ],
            'rewards/notification_rule_website' => [
                'notification_rule_website_id' => 'int(11)',
                'website_id' => 'smallint(5) unsigned',
                'notification_rule_id' => 'int(11)',
            ],
            'rewards/purchase' => [
                'purchase_id' => 'int(11)',
                'quote_id' => 'int(11)',
                'order_id' => 'int(11)',
                'spend_points' => 'int(11)',
                'spend_amount' => 'double',
                'spend_min_points' => 'int(11)',
                'spend_max_points' => 'int(11)',
                'earn_points' => 'int(11)',
            ],
            'rewards/referral' => [
                'referral_id' => 'int(11)',
                'customer_id' => 'int(10) unsigned',
                'new_customer_id' => 'int(10) unsigned',
                'email' => 'varchar(255)',
                'name' => 'varchar(255)',
                'status' => 'varchar(255)',
                'store_id' => 'smallint(5) unsigned',
                'last_transaction_id' => 'int(11)',
                'points_amount' => 'int(11)',
                'created_at' => 'timestamp',
                'quote_id' => 'int(11)',
            ],
            'rewards/spending_rule' => [
                'spending_rule_id' => 'int(11)',
                'name' => 'varchar(255)',
                'description' => 'text',
                'is_active' => 'int(11)',
                'active_from' => 'timestamp',
                'active_to' => 'timestamp',
                'type' => 'varchar(255)',
                'conditions_serialized' => 'text',
                'actions_serialized' => 'text',
                'spending_style' => 'varchar(255)',
                'spend_points' => 'int(11)',
                'monetary_step' => 'double',
                'spend_min_points' => 'varchar(255)',
                'spend_max_points' => 'varchar(255)',
                'sort_order' => 'int(11)',
                'is_stop_processing' => 'tinyint(1)',
            ],
            'rewards/spending_rule_customer_group' => [
                'spending_rule_customer_group_id' => 'int(11)',
                'customer_group_id' => 'smallint(5) unsigned',
                'spending_rule_id' => 'int(11)',
            ],
            'rewards/spending_rule_website' => [
                'spending_rule_website_id' => 'int(11)',
                'website_id' => 'smallint(5) unsigned',
                'spending_rule_id' => 'int(11)',
            ],
            'rewards/transaction' => [
                'transaction_id' => 'int(11)',
                'customer_id' => 'int(10) unsigned',
                'amount' => 'int(11)',
                'amount_used' => 'int(11)',
                'comment' => 'text',
                'code' => 'varchar(255)',
                'is_expired' => 'tinyint(1)',
                'is_expiration_email_sent' => 'tinyint(1)',
                'expires_at' => 'timestamp',
                'created_at' => 'timestamp',
                'updated_at' => 'timestamp',
            ],
        ];

        $dbCheck = $this->dbCheckTables(array_keys($structure));
        if ($dbCheck[0] != self::SUCCESS) {
            return [self::FAILED, 'Database Structure', $dbCheck[2]];
        }

        $title = 'Database Structure';
        $description = [];
        foreach (array_keys($structure) as $tableName) {
            // Pass 0: If table record has empty array - check is not performed
            if (!count($structure[$tableName])) {
                continue;
            }

            // Pass 1: Check for missing fields (sqlResult can not be reset for some reason)
            foreach (array_keys($structure[$tableName]) as $field) {
                $exists = false;
                $sqlResult = $this->_dbConn()->query('DESCRIBE '.$this->_dbRes()->getTableName($tableName).';');
                foreach ($sqlResult as $sqlRow) {
                    if (!$exists && $sqlRow['Field'] == $field) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    $description[] = $this->_dbRes()->getTableName($tableName).' has missing field: '.$field;
                }
            }

            // Pass 2: Check for types and alteration
            $sqlResult = $this->_dbConn()->query('DESCRIBE '.$this->_dbRes()->getTableName($tableName).';');
            foreach ($sqlResult as $sqlRow) {
                if (array_key_exists($sqlRow['Field'], $structure[$tableName])) {
                    if ($sqlRow['Type'] != $structure[$tableName][$sqlRow['Field']]) {
                        $description[] = $this->_dbRes()->getTableName($tableName).
                            ' has different structure, field '.$sqlRow['Field'].' has type '.$sqlRow['Type'];
                    }
                } else {
                    $description[] = $this->_dbRes()->getTableName($tableName).
                        ' was altered and has custom field '.$sqlRow['Field'];
                }
            }
        }

        return (count($description)) ?
            [self::FAILED, $title, array_merge($description, ['Contact Mirasvit Support.'])] :
            [self::SUCCESS, $title, $description];
    }
}
