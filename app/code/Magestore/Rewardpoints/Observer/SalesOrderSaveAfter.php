<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfter implements ObserverInterface
{

    /**
     * Store manager
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_transaction;
    /**
     * Helper Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * SalesOrderSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magestore\Rewardpoints\Helper\Action $action
     * @param \Magestore\Rewardpoints\Model\TransactionFactory $transaction
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer $customer,
        \Magestore\Rewardpoints\Helper\Action $action,
        \Magestore\Rewardpoints\Model\TransactionFactory $transaction
    ){
        $this->_request = $request;
        $this->_customer = $customer;
        $this->_transaction = $transaction;
        $this->_action = $action;
    }
    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        if ($order->getState() == \Magento\Sales\Model\Order::STATE_COMPLETE
            && $order->getRewardpointsEarn()
        ) {
            $customer = $this->_customer->load($order->getCustomerId());
            if (!$customer->getId()) {
                return $this;
            }
            $this->_action->addTransaction(
                'earning_invoice', $customer, $order
            );
            return $this;
        }

        // Check is refund manual
        $input = $this->_request->getParam('creditmemo');
        if (isset($input['refund_points']) || isset($input['refund_earned_points'])) {
            return $this;
        }

        // Refund point that customer used to spend for this order (when order is canceled)
        $refundStatus = (string)$this->_action->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Spending::XML_PATH_ORDER_REFUND_STATUS,
            $order->getStoreId()
        );
        $refundStatus = explode(',', $refundStatus);
        if ($order->getStatus() && in_array($order->getStatus(), $refundStatus)) {
            $maxPoint  = $order->getRewardpointsSpent();
            $maxPoint -= (int)$this->_transaction->create()->getCollection()
                ->addFieldToFilter('action', 'spending_cancel')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            $maxPoint -= (int)$this->_transaction->create()->getCollection()
                ->addFieldToFilter('action', 'spending_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxPoint > 0) {
                $order->setRefundSpentPoints($maxPoint);
                if (empty($customer)) {
                    $customer = $this->_customer->load($order->getCustomerId());
                }
                if (!$customer->getId()) {
                    return $this;
                }
                $this->_action->addTransaction(
                    'spending_cancel', $customer, $order
                );
            }
        }

        // Deduct earning point from customer if order is canceled
        $refundStatus = (string)$this->_action->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_ORDER_CANCEL_STATUS,
            $order->getStoreId()
        );
        $refundStatus = explode(',', $refundStatus);
        if ($order->getStatus() && in_array($order->getStatus(), $refundStatus)) {
            if ($order->getRewardpointsEarn() <= 0) {
                return $this;
            }
            /*  */
            $maxEarnedRefund  = (int)$this->_transaction->create()->getCollection()
                ->addFieldToFilter('action', 'earning_invoice')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            $maxEarnedRefund += (int)$this->_transaction->create()->getCollection()
                ->addFieldToFilter('action', 'earning_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            $maxEarnedRefund += (int)$this->_transaction->create()->getCollection()
                ->addFieldToFilter('action', 'earning_cancel')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxEarnedRefund <= 0) {
                return $this;
            }
            if ($maxEarnedRefund > $order->getRewardpointsEarn()) {
                $maxEarnedRefund = $order->getRewardpointsEarn();
            }
            if ($maxEarnedRefund > 0) {
                $order->setRefundEarnedPoints($maxEarnedRefund);
                if (empty($customer)) {
                    $customer = $this->_customer->load($order->getCustomerId());
                }
                if (!$customer->getId()) {
                    return $this;
                }
                $this->_action->addTransaction(
                    'earning_cancel', $customer, $order
                );
            }
        }

        return $this;
    }
}