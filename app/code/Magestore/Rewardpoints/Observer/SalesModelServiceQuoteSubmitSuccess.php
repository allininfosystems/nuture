<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkOutSession;
    /**
     * Helper Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesModelServiceQuoteSubmitAfter constructor.
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magestore\Rewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magestore\Rewardpoints\Helper\Action $action
    ){
        $this->_checkOutSession = $session;
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
        $quote = $observer['quote'];
        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        // Process spending points for order
        if ($order->getRewardpointsSpent() > 0) {
            $this->_action->addTransaction('spending_order',
                $quote->getCustomer(),
                $order
            );
        }

        // Clear reward points checkout session
        $session = $this->_checkOutSession;
        $session->setCatalogRules(array());
        $session->setData('use_point', 0);
        $session->setRewardSalesRules(array());
        $session->setRewardCheckedRules(array());

        return $this;
    }
}