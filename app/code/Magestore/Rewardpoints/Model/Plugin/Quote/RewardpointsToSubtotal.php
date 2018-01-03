<?php

namespace Magestore\Rewardpoints\Model\Plugin\Quote;

class RewardpointsToSubtotal
{
    public function afterGetSubtotalWithDiscount(\Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $grid, $result)
    {
        $address = $grid->getQuoteAddress();
        return $result + $address->getRewardpointsAmount();
    }

    public function afterGetDiscountAmount(\Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $grid, $result)
    {
        $address = $grid->getQuoteAddress();
        return $result + $address->getRewardpointsAmount();
    }
}