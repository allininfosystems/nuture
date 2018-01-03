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
namespace Magestore\Rewardpoints\Model\Total\Quote;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;

class ResetRewardpoints implements ObserverInterface
{
    /**
     * Set Quote information about rewardpoints
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setRewardpointsSpent(0);
        $quote->setRewardpointsBaseDiscount(0);
        $quote->setRewardpointsDiscount(0);
        $quote->setRewardpointsEarn(0);
        $quote->setMagestoreBaseDiscount(0);
        $quote->setRewardpointsBaseAmount(0);
        $quote->setRewardpointsAmount(0);
        $quote->setMagestoreBaseDiscountForShipping(0);
        $quote->setMagestoreBaseDiscount(0);
        foreach ($quote->getAllAddresses() as $address) {
            $address->setRewardpointsSpent(0);
            $address->setRewardpointsBaseDiscount(0);
            $address->setRewardpointsDiscount(0);
            $address->setRewardpointsBaseAmount(0);
            $address->setRewardpointsAmount(0);
            $address->setMagestoreBaseDiscountForShipping(0);
            $address->setMagestoreBaseDiscount(0);
            $address->setRewardpointsEarn(0);
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId())
                    continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setRewardpointsBaseDiscount(0)
                            ->setRewardpointsDiscount(0)
                            ->setMagestoreBaseDiscount(0)
                            ->setRewardpointsEarn(0)
                            ->setRewardpointsSpent(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setRewardpointsBaseDiscount(0)
                        ->setRewardpointsDiscount(0)
                        ->setMagestoreBaseDiscount(0)
                        ->setRewardpointsEarn(0)
                        ->setRewardpointsSpent(0);
                }
            }
        }
    }
}
