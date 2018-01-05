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

use Magento\Framework\Event\ObserverInterface;

class AddVarsToEmail implements ObserverInterface
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Purchase $purchase
    ) {
        $this->purchase = $purchase;
    }

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
        $variables = $observer->getEvent()->getTransport();
        if (is_object($variables)) {
            $variables = $variables->getData();
        }

        if (isset($variables['order']) && $variables['order'] instanceof \Magento\Sales\Api\Data\OrderInterface) {
            /** @var \Magento\Sales\Api\Data\OrderInterface $order */
            $order = $variables['order'];
        } else {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        $purchase = $this->purchase->getByOrder($order);
        $order->setRewardsEarnedPoints($purchase->getEarnPoints());
        $order->setRewardsSpentPoints($purchase->getSpendPoints());
        $order->setRewardsSpentAmount($purchase->getSpendAmount());
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
