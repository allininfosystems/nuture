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



require __DIR__.'/customer.php';

$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_rewards_customer_referral_link')
);
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_rewards_customer_referral_link').' AUTO_INCREMENT = 1;'
);


/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $t \Mirasvit\Rewards\Model\Transaction */
$t = $objectManager->create('Mirasvit\Rewards\Model\ReferralLink');
$t->setData(
    [
        'customer_id' => 1,
        'referral_link' => 'df78g98dfgd9fg6d987fg8d',
    ]
)->save();
