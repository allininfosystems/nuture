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

$rule = $objectManager->create('Mirasvit\Rewards\Model\Earning\Rule');

/* @var Mirasvit\Rewards\Model\Earning\Rule $rule */
$rule
    ->setName('Test Earning Rule Pinterest')
    ->setType('behavior')
    ->setIsActive(1)
    ->setCustomerGroupIds([1])
    ->setWebsiteIds([1])
    ->setBehaviorTrigger('pinterest_pin')
    ->setEarnPoints(15)
    ->setPointsLimit(0)
    ->setHistoryMessage('test 15pts')
    ->setEmailMessage('test earning rule pinterest')
    ->setRule([
        'conditions' => [
            '1' => [
                'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ],
            '1--1' => [
                'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer',
                'attribute' => 'orders_number',
                'operator' => '==',
                'value' => 1,
            ],
        ],
    ]);

$rule->isObjectNew(true);
$rule->save();
