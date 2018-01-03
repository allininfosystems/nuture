<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer\Backend;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoSaveAfter implements ObserverInterface
{

    /**
     * Store manager
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * Helper Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesOrderInvoiceSaveAfter constructor.
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magestore\Rewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Magestore\Rewardpoints\Helper\Action $action
    ){
        $this->_customer = $customer;
        $this->_action = $action;
    }
    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $creditmemo = $observer['creditmemo'];
        $order      = $creditmemo->getOrder();

        // Refund spent points
        if ($creditmemo->getRefundSpentPoints() > 0) {
            $customer = $this->_customer->load($order->getCustomerId());
            if ($customer->getId()) {
                $this->_action->addTransaction(
                    'spending_creditmemo', $customer, $creditmemo
                );
            }
        }

        // Deduce earned points
        if ($creditmemo->getRefundEarnedPoints() > 0) {
            if (empty($customer)) {
                $customer = $this->_customer->load($order->getCustomerId());
            }
            if ($customer->getId()) {
                $this->_action->addTransaction(
                    'earning_creditmemo', $customer, $creditmemo
                );
            }
        }

        return $this;
    }
}