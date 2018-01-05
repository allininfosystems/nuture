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



namespace Mirasvit\Rewards\Block\Adminhtml\Sales\Order\Create;

class Payment extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsHelper,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Magento\Sales\Model\AdminOrder\Create $salesOrderCreate,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->rewardsBalance   = $rewardsBalance;
        $this->rewardsHelper    = $rewardsHelper;
        $this->rewardsPurchase  = $rewardsPurchase;
        $this->salesOrderCreate = $salesOrderCreate;
        $this->sessionQuote     = $sessionQuote;
        $this->context          = $context;
        $this->purchase         = $this->rewardsPurchase->getByQuote($this->getOrderQuote());

        if ($this->purchase && !$this->purchase->getSpendAmount()) {
            $this->purchase->refreshPointsNumber(true);
        }

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getOrderQuote()
    {
        return $this->salesOrderCreate->getQuote();
    }

    /**
     * @return string
     */
    public function getApplyUrl()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function canUseRewardsPoints()
    {
        if (!$this->getOrderQuote()->getCustomerId()) {
            return false;
        }

        return (bool)$this->getMaxPointsToSpent();
    }

    /**
     * @return int
     */
    public function getMaxPointsToSpent()
    {
        return $this->purchase->getSpendMaxPoints();
    }

    /**
     * @return int
     */
    public function getPointsAmount()
    {
        return $this->purchase->getSpendPoints();
    }

    /**
     * @return int
     */
    public function getBalancePoints()
    {
        return $this->rewardsBalance->getBalancePoints($this->getOrderQuote()->getCustomerId());
    }

    /**
     * @param float $points
     * @return string
     */
    public function formatPoints($points)
    {
        return $this->rewardsHelper->formatPoints($points);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $customer = $this->getOrderQuote()->getCustomer();

        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }
}
