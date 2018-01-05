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



/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_rewards_spending_rule').' AUTO_INCREMENT = 1;'
);

$rule = $objectManager->create('Mirasvit\Rewards\Model\Spending\Rule');

/* @var Mirasvit\Rewards\Model\Spending\Rule $rule */
$rule
//    ->setId(1)
    ->setName('Test Spending Rule')
    ->setType('behavior')
    ->setIsActive(1)
    ->setCustomerGroupIds([1])
    ->setWebsiteIds([1])
    ->setBehaviorTrigger('newsletter_signup')
    ->setEarnPoints(10)
    ->setPointsLimit(0)
    ->setEmailMessage('test Spending Rule')
    ->setSpendPoints(4)
    ->setMonetaryStep(3)
    ->setRule([
        'conditions' => [
            '1' => [
                'type' => '\Mirasvit\Rewards\Model\Spending\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                'attribute' => 'base_subtotal',
                'operator' => '>',
                'value' => 1,
            ],
        ],
        'actions' => [
            '1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                'attribute' => 'quote_item_qty',
                'operator' => '>',
                'value' => 1,
            ],
        ],
    ])
;

$rule->isObjectNew(true);
$rule->save();

$rule = $objectManager->create('Mirasvit\Rewards\Model\Spending\Rule');

/* @var Mirasvit\Rewards\Model\Spending\Rule $rule */
$rule
//    ->setId(2)
    ->setName('Test Spending Rule2')
    ->setType('behavior')
    ->setIsActive(1)
    ->setCustomerGroupIds([1])
    ->setWebsiteIds([1])
    ->setBehaviorTrigger('facebook_like')
    ->setEarnPoints(15)
    ->setPointsLimit(0)
    ->setEmailMessage('test Spending Rule2')
    ->setSpendPoints(2)
    ->setMonetaryStep(1)
    ->setRule([
        'conditions' => [
            '1' => [
                'type' => '\Mirasvit\Rewards\Model\Spending\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                'attribute' => 'base_subtotal',
                'operator' => '>',
                'value' => 2,
            ],
        ],
        'actions' => [
            '1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                'attribute' => 'quote_item_qty',
                'operator' => '>',
                'value' => 2,
            ],
        ],
    ]);

$rule->isObjectNew(true);
$rule->save();
