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



namespace Mirasvit\Rewards\Helper;

use Mirasvit\Rewards\Model\Config as Config;

class Referral extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory
     */
    protected $referralLinkCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Model\ReferralFactory                              $referralFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory $referralLinkCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                              $customerFactory
     * @param \Magento\Store\Model\StoreFactory                                    $storeFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory     $referralCollectionFactory
     * @param \Magento\Customer\Model\Session                                      $session
     * @param \Mirasvit\Rewards\Helper\Behavior                                    $rewardsBehavior
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager
     * @param \Magento\Framework\App\Helper\Context                                $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ReferralFactory $referralFactory,
        \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory $referralLinkCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory,
        \Magento\Customer\Model\Session $session,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->referralFactory = $referralFactory;
        $this->referralLinkCollectionFactory = $referralLinkCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->storeFactory = $storeFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->session = $session;
        $this->rewardsBehavior = $rewardsBehavior;
        $this->storeManager = $storeManager;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param array                            $invitations
     * @param string                           $message
     * @return array
     */
    public function frontendPost($customer, $invitations, $message)
    {
        $rejectedEmails = [];
        foreach ($invitations as $email => $name) {
            $referrals = $this->referralCollectionFactory->create()
                ->addFieldToFilter('email', $email);
            if ($referrals->count()) {
                $rejectedEmails[] = $email;
                continue;
            }

            $message = nl2br(strip_tags($message));

            /** @var  \Mirasvit\Rewards\Model\Referral $referral */
            $referral = $this->referralFactory->create()
                        ->setName($name)
                        ->setEmail($email)
                        ->setCustomerId($customer->getId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->save();
            $referral->sendInvitation($message);
        }

        return $rejectedEmails;
    }

    /**
     * Remember referer when customer adds product to cart.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return void
     */
    public function rememberReferal($quote)
    {
        //if we have referral, we save quote id
        if ($id = (int) $this->session->getReferral()) {
            $referral = $this->referralFactory->create()->load($id);
            if (!$referral->getQuoteId()) {
                $referral->setQuoteId($quote->getId());
                $referral->save();
            }
        }
    }

    /**
     * Find possible \Mirasvit\Rewards\Model\Referral for this order.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Mirasvit\Rewards\Model\Referral
     */
    public function loadReferral($order)
    {
        $quoteId = $order->getQuoteId();
        $referrals = $this->referralCollectionFactory->create()
                        ->addFieldToFilter('quote_id', $quoteId);
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        $referrals = $this->referralCollectionFactory->create()
            ->addFieldToFilter('email', $order->getCustomerEmail());
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        $customerId = $order->getCustomerId();
        $referrals = $this->referralCollectionFactory->create()
                        ->addFieldToFilter('new_customer_id', $customerId);
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        return false;
    }

    /**
     * Customer A refers customer B. Customer B has placed this order.
     * This function can give points to customer A.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    public function processReferralOrder($order)
    {
        if (!$referral = $this->loadReferral($order)) {
            return false;
        }
        /* @var  \Magento\Customer\Model\Customer $customer - customer A */
        if ($customerId = $order->getCustomerId()) {
            $customer = $this->customerFactory->create()->load($customerId);
        } else {
            $customer = new \Magento\Framework\DataObject();
            $customer->setIsGuest(true)
                    ->setEmail($order->getCustomerEmail())
                    ->setFirstname($order->getCustomerFirstname())
                    ->setLastname($order->getCustomerLastname());
        }

        $websiteId = $this->storeFactory->create()->load($order->getStoreId())->getWebsiteId();
        $transaction = $this->rewardsBehavior->processRule(
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER,
            $referral->getCustomerId(),
            $websiteId,
            $order->getId(),
            ['referred_customer' => $customer, 'order' => $order]
        );
        $referral->finish(Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER, $customerId, $transaction);
    }

    /**
     * @return string
     */
    public function getReferralLinkId()
    {
        $link = $this->referralLinkCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->session->getCustomerId())
            ->getFirstItem();
        //if we haven't generated link, create it
        if (!$link->getId()) {
            $link->createReferralLinkId($this->session->getCustomerId());
        }

        return $link->getReferralLink();
    }
}
