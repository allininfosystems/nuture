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
namespace Magestore\Rewardpoints\Observer\Backend;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;

class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Customer
     *
     * @var \Magestore\Rewardpoints\Model\Customer
     */

    protected $_rewardAccount;

    /**
     * Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */

    protected $_action;

    /**
     * customer
     *
     * @var \Magento\Customer\Model\CustomerFactory ,
     */

    protected $_customerFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magestore\Rewardpoints\Model\Customer $rewardAccount,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Rewardpoints\Helper\Action $action
    )
    {
        $this->_request = $request;
        $this->_rewardAccount = $rewardAccount;
        $this->_customerFactory = $customerFactory;
        $this->_action = $action;
        $this->_messageManager = $messageManager;
    }
    /**
     * Update the point balance to customer's account
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer->getId()) {
            return;
        }
        $params = $this->_request->getParam('rewardpoints');
        if (empty($params['admin_editing'])) {
            return;
        }

        // Update reward account settings
        $rewardAccount = $this->_rewardAccount->load($customer->getId(), 'customer_id');
        $rewardAccount->setCustomerId($customer->getId());
        if (!$rewardAccount->getId()) {
            $rewardAccount->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0);
        }
        $params['is_notification'] = empty($params['is_notification']) ? 0 : 1;
        $params['expire_notification'] = empty($params['expire_notification']) ? 0 : 1;
        $rewardAccount->setData('is_notification', $params['is_notification'])
            ->setData('expire_notification', $params['expire_notification']);
        try {
            $rewardAccount->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_messageManager->addError(__($e->getMessage()));
        }

        // Create transactions for customer if need
        if (!empty($params['change_balance'])) {
            try {
                $this->_action->addTransaction('admin', $customer, new \Magento\Framework\DataObject(array(
                        'point_amount' => $params['change_balance'],
                        'title' => $params['change_title'],
                        'expiration_day' => (int) $params['expiration_day'],
                    ))
                );
            } catch (\Exception $e) {
                $this->_messageManager->addError(__("An error occurred while changing the customer's point balance."));
                $this->_messageManager->addError($e->getMessage());
            }
        }

        return $this;
    }
}
