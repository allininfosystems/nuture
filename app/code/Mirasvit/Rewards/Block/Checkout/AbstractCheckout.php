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



namespace Mirasvit\Rewards\Block\Checkout;

class AbstractCheckout extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @param \Mirasvit\Rewards\Helper\Purchase                $rewardsPurchase
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->rewardsPurchase = $rewardsPurchase;
        $this->customerSession = $customerSession;
        $this->context = $context;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
    }

    /**
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    protected function getPurchase()
    {
        return $this->rewardsPurchase->getPurchase();
    }

    /**
     * @return int
     */
    public function getEarnPoints()
    {
        if (!$this->getPurchase()) {
            return 0;
        }

        return $this->getPurchase()->getEarnPoints();
    }

    /**
     * Get logged in customer.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->customerSession->getCustomer();
        }

        return $this->customer;
    }

    /**
     * @return int
     * @deprecated
     */
    public function getPointsEarned()
    {
        return $this->getEarnPoints();
    }
}
