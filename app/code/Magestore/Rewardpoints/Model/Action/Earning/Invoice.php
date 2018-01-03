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
namespace Magestore\Rewardpoints\Model\Action\Earning;
/**
 * Action Earn Point for Order
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */

class Invoice extends \Magestore\Rewardpoints\Model\Action\AbstractAction implements \Magestore\Rewardpoints\Model\Action\InterfaceAction
{
    /**
     * Calculate and return point amount that customer earned from order
     * 
     * @return int
     */
    public function getPointAmount()
    {
        $invoice = $this->getData('action_object');
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $order = $invoice->getOrder();
            $isInvoice = true;
        } else {
            $order = $invoice;
            $isInvoice = false;
        }
        
        $maxEarn  = $order->getRewardpointsEarn();
		
		$cancelPoint =0;
		foreach ($order->getAllItems() as $item) {
			 $itemPoint  = (int)$item->getRewardpointsEarn();
             $cancelPoint  += $itemPoint * ($item->getQtyCanceled()+$item->getQtyRefunded()) / $item->getQtyOrdered();
		}
		$maxEarn = $maxEarn - floor($cancelPoint);
		
        $maxEarn -= (int)$this->_transaction->create()->getCollection()
            ->addFieldToFilter('action', 'earning_invoice')
            ->addFieldToFilter('order_id', $order->getId())
            ->getFieldTotal();
        if ($maxEarn <= 0) {
            return 0;
        }
        
        if (!$isInvoice) {
            return (int)$maxEarn;
        }
        return $invoice->getRewardpointsEarn();
    }
    
    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel()
    {
        return __('Earn points for purchasing order');
    }
    
    public function getActionType()
    {
        return \Magestore\Rewardpoints\Model\Transaction::ACTION_TYPE_EARN;
    }
    
    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle()
    {
        $invoice = $this->getData('action_object');
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $order = $invoice->getOrder();
        } else {
            $order = $invoice;
        }
        return __('Earn points for purchasing order #%1', $order->getIncrementId());
    }
    
    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Magestore_RewardPoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        if (is_null($transaction)) {
            return $this->getTitle();
        }
        if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
            $editUrl = $this->_urlBuilder->getUrl('adminhtml/sales_order/view', array('order_id' => $transaction->getOrderId()));
        } else {
            $editUrl = $this->_urlBuilder->getUrl('sales/order/view', array('order_id' => $transaction->getOrderId()));
        }
        return __(
            'Earn points for purchasing order %1',
            '<a href="' . $editUrl .'" title="'
            . __('View Order')
            . '">#' . $transaction->getOrderIncrementId() . '</a>'
        );
    }
    
    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Magestore_RewardPoints_Model_Action_Interface
     */
    public function prepareTransaction()
    {
        $invoice = $this->getData('action_object');
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $order = $invoice->getOrder();
        } else {
            $order = $invoice;
        }
        
        $transactionData = array(
            'status'    => \Magestore\Rewardpoints\Model\Transaction::STATUS_COMPLETED,
            'order_id'  => $order->getId(),
            'order_increment_id'    => $order->getIncrementId(),
            'order_base_amount'     => $order->getBaseGrandTotal(),
            'order_amount'          => $order->getGrandTotal(),
            'base_discount'         => $invoice->getRewardpointsBaseDiscount(),
            'discount'              => $invoice->getRewardpointsDiscount(),
            'store_id'  => $order->getStoreId(),
        );
        
        // Check if transaction need to hold
        $holdDays = (int)$this->_helper->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_HOLDING_DAYS,
            $order->getStoreId()
        );
        if ($holdDays > 0) {
            $transactionData['status'] = \Magestore\Rewardpoints\Model\Transaction::STATUS_ON_HOLD;
        }
        
        // Set expire time for current transaction
        $expireDays = (int)$this->_helper->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_EXPIRE,
            $order->getStoreId()
        );
        $transactionData['expiration_date'] = $this->getExpirationDate($expireDays);
        
        // Set invoice id for current earning
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $transactionData['extra_content'] = $invoice->getIncrementId();
        }
        
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
