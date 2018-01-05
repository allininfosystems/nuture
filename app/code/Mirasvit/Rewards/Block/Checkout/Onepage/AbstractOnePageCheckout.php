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



namespace Mirasvit\Rewards\Block\Checkout\Onepage;

abstract class AbstractOnePageCheckout extends \Magento\Checkout\Block\Cart\AbstractCart
{
    public function __construct(
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->sessionFactory  = $sessionFactory;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsBalance  = $rewardsBalance;
        $this->customerSession = $customerSession;
        $this->context         = $context;

        parent::__construct($context, $customerSession, $checkoutSession, $data);
    }

    /**
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    protected function getPurchase()
    {
        $purchase = $this->rewardsPurchase->getByQuote($this->getQuote());

        return $purchase;
    }

    /**
     * @return int
     * @deprecated method renamed
     */
    public function getPointsAmount()
    {
        if (!$this->getPurchase()) {
            return 0;
        }

        return $this->getPurchase()->getSpendPoints();
    }

    /**
     * @return int
     */
    public function getBalancePoints()
    {
        return $this->rewardsBalance->getBalancePoints($this->customerSession->getCustomer());
    }

    /**
     * @return int
     */
    public function getMaxPointsNumberToSpent()
    {
        if (!$this->getPurchase()) {
            return 0;
        }

        return $this->getPurchase()->getMaxPointsNumberToSpent();
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if (!$this->sessionFactory->create()->isLoggedIn()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getApplyUrl()
    {
        return $this->getUrl('rewards/checkout/applyPointsPost', ['_secure' => true]);
    }
}
