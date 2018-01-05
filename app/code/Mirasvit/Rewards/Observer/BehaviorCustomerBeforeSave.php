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

class BehaviorCustomerBeforeSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mirasvit\Rewards\Helper\Behavior       $rewardsBehavior
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->request         = $request;
        $this->rewardsBehavior = $rewardsBehavior;
    }

    /**
     * We need this, to correctly give points for sign up.
     * See also \Mirasvit\Rewards\Observer\BehaviorCustomerAfterCommit
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();
        $customer->_customerNew = false;
        if ($customer->isObjectNew()) {
            $customer->_customerNew = true;
        }

        $this->updateSubscription($customer);
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void
     */
    public function updateSubscription($customer)
    {
        if ($this->request->getParam('rewards_subscription_key', false)) {
            $customer->setData('rewards_subscription', (int)$this->request->getParam('rewards_subscription', false));
        }
    }
}
