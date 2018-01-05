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

class AdminOrderQuoteAfterSave implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Mirasvit\Rewards\Helper\Referral
     */
    protected $rewardsReferral;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Customer\Model\Session   $customerSession
     * @param \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase
     * @param \Mirasvit\Rewards\Helper\Referral $rewardsReferral
     * @param \Mirasvit\Rewards\Model\Config    $config
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Referral $rewardsReferral,
        \Mirasvit\Rewards\Model\Config $config
    ) {
        $this->customerSession = $customerSession;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsReferral = $rewardsReferral;
        $this->config = $config;
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
        if ($this->config->getQuoteSaveFlag()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        if (!$quote = $observer->getQuote()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        $this->config->setQuoteSaveFlag(true);
        $this->refreshPoints($quote);
        $this->config->setQuoteSaveFlag(false);
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return void
     */
    protected function refreshPoints($quote)
    {
        if ($quote->getIsPurchaseSave()) {
            return;
        }

        if (!$purchase = $this->rewardsPurchase->getByQuote($quote)) {
            return;
        }

        $purchase->refreshPointsNumber();
    }
}
