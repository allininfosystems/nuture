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
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductGetFinalPrice
 * @package Magestore\Affiliateplus\Observer
 */
class FieldSet implements ObserverInterface
{

    public $_helperPoint;
    public $_helperCustomer;
    public $_helperSpending;

    /**
     * FieldSet constructor.
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending
    ){
        $this->_helperPoint = $helperPoint;
        $this->_helperCustomer = $helperCustomer;
        $this->_helperSpending = $helperSpending;
    }
     /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        $order->setRewardpointsEarn($quote->getRewardpointsEarn())
            ->setRewardpointsSpent($quote->getRewardpointsSpent())
            ->setRewardpointsBaseDiscount($quote->getRewardpointsBaseDiscount())
            ->setRewardpointsDiscount($quote->getRewardpointsDiscount())
            ->setRewardpointsBaseAmount($quote->getRewardpointsBaseAmount())
            ->setRewardpointsAmount($quote->getRewardpointsAmount());

        // Validate point amount before place order
        $totalPointSpent = $this->_helperSpending->getTotalPointSpent();
        if (!$totalPointSpent) {
            return $this;
        }

        $balance = $this->_helperCustomer->getBalance();
        if ($balance < $totalPointSpent) {
            throw new \Exception(__(
                'Your points balance is not enough to spend for this order'
            ));
        }

        $minPoint = (int)$this->_helperPoint->getConfig(
            \Magestore\RewardPoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS,
            $quote->getStoreId()
        );
        if ($minPoint > $balance) {
            throw new \Exception(__(
                'Minimum points balance allows to redeem is %s',
                $this->_helperPoint->format($minPoint, $quote->getStoreId())
            ));
        }

        $applyTaxAfterDiscount = (bool) $this->_helperPoint->getConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId());

        if ($applyTaxAfterDiscount) {
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                            $child->setDiscountAmount($child->getDiscountAmount()-$child->getRewardpointsDiscount());
                            $child->setBaseDiscountAmount($child->getBaseDiscountAmount()-$child->getRewardpointsBaseDiscount());
                    }
                } elseif ($item->getProduct()) {
                        $item->setDiscountAmount($item->getDiscountAmount()-$item->getRewardpointsDiscount());
                        $item->setBaseDiscountAmount($item->getBaseDiscountAmount()-$item->getRewardpointsBaseDiscount());
                }
            }
            $order->setBaseShippingDiscountAmount($order->getBaseShippingDiscountAmount()-$quote->getRewardpointsBaseAmount());
            $order->setShippingDiscountAmount($order->getShippingDiscountAmount()-$quote->getRewardpointsAmount());
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                            $child->setDiscountAmount($child->getDiscountAmount()-$child->getRewardpointsDiscount());
                            $child->setBaseDiscountAmount($child->getBaseDiscountAmount()-$child->getRewardpointsBaseDiscount());
                    }
                } elseif ($item->getProduct() && !$item->getParentItem()) {
                        $item->setDiscountAmount($item->getDiscountAmount()-$item->getRewardpointsDiscount());
                        $item->setBaseDiscountAmount($item->getBaseDiscountAmount()-$item->getRewardpointsBaseDiscount());
                }
            }
        }
    }
}
