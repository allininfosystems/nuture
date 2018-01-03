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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Model\Total\Creditmemo;
/**
 * Rewardpoints Spend for Order by Point Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Point extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this|void
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setRewardpointsDiscount(0);
        $creditmemo->setRewardpointsBaseDiscount(0);

        $order = $creditmemo->getOrder();
        
        if ($order->getRewardpointsDiscount() < 0.0001) {
            return ;
        }

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;
        $baseTotalDiscountRefunded = 0;
        $totalDiscountRefunded = 0;
        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getRewardpointsDiscount()) {
                $totalDiscountRefunded     += $existedCreditmemo->getRewardpointsDiscount();
                $baseTotalDiscountRefunded += $existedCreditmemo->getRewardpointsBaseDiscount();
            }
        }

        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmount = $baseShippingAmount * $order->getRewardpointsBaseAmount() / $order->getBaseShippingAmount();
            $totalDiscountAmount = $order->getShippingAmount() * $baseTotalDiscountAmount / $order->getBaseShippingAmount();
        }
        
        if ($this->isLast($creditmemo)) {
            $baseTotalDiscountAmount   = $order->getRewardpointsBaseDiscount() - $baseTotalDiscountRefunded;
            $totalDiscountAmount       = $order->getRewardpointsDiscount() - $totalDiscountRefunded;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $orderItemDiscount      = (float) $orderItem->getRewardpointsDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                $baseOrderItemDiscount  = (float) $orderItem->getRewardpointsBaseDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                
                $orderItemQty           = $orderItem->getQtyInvoiced();

                if ($orderItemDiscount && $orderItemQty) {                    
                    $totalDiscountAmount += $creditmemo->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $creditmemo->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                }
            }
        }

        $creditmemo->setRewardpointsDiscount($totalDiscountAmount);
        $creditmemo->setRewardpointsBaseDiscount($baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);// + $totalHiddenTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);// + $baseTotalHiddenTax);
        return $this;
    }
    
    /**
     * check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}
