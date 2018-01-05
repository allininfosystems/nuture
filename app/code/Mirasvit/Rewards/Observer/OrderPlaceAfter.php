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



namespace Mirasvit\Rewards\Observer;

class OrderPlaceAfter extends Order
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var \Mirasvit\Rewards\Model\Purchase $purchase */
        $purchase = $this->rewardsPurchase->getByQuote($order->getQuoteId());
        $this->refreshPoints($purchase->getQuote(), true);
        if ($order && $order->getId()) {
            if ($this->_isOrderPaidNow($order)) {
                if ($order->getCustomerId()) {
                    $this->rewardsBalanceOrder->spendOrderPoints($order);
                }
            }
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
