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
    'ALTER TABLE '.$installer->getTableName('mst_rewards_notification_rule').' AUTO_INCREMENT = 1;'
);
$installer->getConnection()->query('DELETE FROM '.$installer->getTableName('mst_rewards_notification_rule'));

$rule = $objectManager->create('Mirasvit\Rewards\Model\Notification\Rule');

/* @var Mirasvit\Rewards\Model\Notification\Rule $rule */
$rule
//    ->setId(1)
    ->setName('Test Notification Rule')
    ->setIsActive(1)
    ->setCustomerGroupIds([1])
    ->setWebsiteIds([1])
    ->setEmailMessage('test notification rule email')
    ->setRule([
        'conditions' => [
            '1' => [
                'type' => '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => '\Magento\SalesRule\Model\Rule\Condition\Address',
                'attribute' => 'weight',
                'operator' => '<',
                'value' => 752,
            ],
        ],
    ]);

$rule->isObjectNew(true);
$rule->save();

$rule = $objectManager->create('Mirasvit\Rewards\Model\Notification\Rule');

/* @var Mirasvit\Rewards\Model\Notification\Rule $rule */
$rule
//    ->setId(2)
    ->setName('Test Notification Rule2')
    ->setIsActive(1)
    ->setCustomerGroupIds([1])
    ->setWebsiteIds([1])
    ->setEmailMessage('test notification rule2 email')
    ->setRule([
        'conditions' => [
            '1' => [
                'type' => '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => '\Magento\SalesRule\Model\Rule\Condition\Address',
                'attribute' => 'weight',
                'operator' => '<',
                'value' => 500,
            ],
        ],
    ]);

$rule->isObjectNew(true);
$rule->save();
