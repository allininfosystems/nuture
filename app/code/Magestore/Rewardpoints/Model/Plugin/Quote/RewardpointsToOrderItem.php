<?php

namespace Magestore\Rewardpoints\Model\Plugin\Quote;

class RewardpointsToOrderItem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        if ($item->getRewardpointsEarn()) {
            $orderItem->setRewardpointsEarn($item->getRewardpointsEarn());
        }
        if ($item->getRewardpointsSpent()) {
            $orderItem->setRewardpointsSpent($item->getRewardpointsSpent());
            $orderItem->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount());
            $orderItem->setRewardpointsDiscount($item->getRewardpointsDiscount());
        }

        return $orderItem;
    }
}