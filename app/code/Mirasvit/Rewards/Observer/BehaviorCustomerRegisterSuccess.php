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
use Mirasvit\Rewards\Model\Config;

class BehaviorCustomerRegisterSuccess implements ObserverInterface
{
    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber
     */
    protected $subscriberModel;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Mirasvit\Rewards\Model\ReferralFactory $referralFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory,
        \Magento\Newsletter\Model\ResourceModel\Subscriber $subscriberModel,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->session                   = $session;
        $this->referralFactory           = $referralFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->subscriberModel           = $subscriberModel;
        $this->rewardsBehavior           = $rewardsBehavior;
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
        if (substr(php_sapi_name(), 0, 3) == 'cli') {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customerDataObject = $observer->getEvent()->getCustomerDataObject();
        $origCustomerDataObject = $observer->getEvent()->getOrigCustomerDataObject();
        if (!$origCustomerDataObject || !$origCustomerDataObject->getId()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        if (!$customerDataObject->getId()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        $customer = $customerDataObject;

        $this->applyRules($customer);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return void
     */
    protected function applyRules($customer)
    {
        $this->customerAfterCreate($customer);
        $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_SIGNUP, $customer);
        if ($this->isCustomerSubscribed($customer)) {
            $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP, $customer);
        }
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return bool
     */
    protected function isCustomerSubscribed($customer)
    {
        $subscribed = false;
        $subscriber = $this->subscriberModel->loadByEmail($customer->getEmail());
        if ($subscriber && $subscriber['subscriber_status']) {
            $subscribed = true;
        }

        return $subscribed;
    }

    /**
     * Customer sign up.
     *
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return void
     */
    public function customerAfterCreate($customer)
    {
        $referral = false;
        if ($id = (int) $this->session->getReferral()) {
            /** @var \Mirasvit\Rewards\Model\Referral $referral */
            $referral = $this->referralFactory->create()->load($id);
        } else {
            $referrals = $this->referralCollectionFactory->create()
                ->addFieldToFilter('email', $customer->getEmail());
            if ($referrals->count()) {
                $referral = $referrals->getFirstItem();
            }
        }
        if (!$referral) {
            return;
        }
        $referral->finish(Config::REFERRAL_STATUS_SIGNUP, $customer->getId());
        /** @var \Mirasvit\Rewards\Model\Transaction $transaction */
        $transaction = $this->rewardsBehavior->processRule(
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP,
            $referral->getCustomerId(),
            false,
            $customer->getId()
        );
        $referral->finish(Config::REFERRAL_STATUS_SIGNUP, false, $transaction);
    }
}
