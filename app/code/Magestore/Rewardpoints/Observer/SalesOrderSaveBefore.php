<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveBefore implements ObserverInterface
{
    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order= $observer->getEvent()->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            $order->setRewardpointsEarn(0);
            foreach ($order->getAllItems() as $item){
                if ($item->getParentItemId())
                    continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $child->setRewardpointsEarn(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setRewardpointsEarn(0);
                }
            }
            return $this;
        }

        return $this;
    }
}