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
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Observer\Backend;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoRefund implements ObserverInterface
{
    /**
     * Update the point balance to customer's account
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer['creditmemo'];
        $order = $creditmemo->getOrder();
        if ($order->getRewardpointsSpent() && $order->getForcedCanCreditmemo()) {
            $order->setForcedCanCreditmemo(false);
        }
    }
}
