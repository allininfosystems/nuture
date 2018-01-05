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


/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_rewards_transaction')
);
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_rewards_transaction').' AUTO_INCREMENT = 1;'
);

$customerId = 1;
if (!empty($customer) && !empty($customer->getId())) {
    $customerId = $customer->getId();
}

/** @var $t \Mirasvit\Rewards\Model\Transaction */
$t = $objectManager->create('Mirasvit\Rewards\Model\Transaction');
$t->setData(
  [
      'customer_id' => $customerId,
      'amount' => 1,
      'history_message' => 'test transactions',
      'email_message' => 'test transactions email',
  ]
)->save();

$t = $objectManager->create('Mirasvit\Rewards\Model\Transaction');
$t->setData(
    [
        'customer_id' => $customerId,
        'amount' => 3,
        'history_message' => 'test transactions2',
        'email_message' => 'test transactions2 email',
    ]
);
$t->save();
