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

class Spend extends \Mirasvit\Rewards\Block\Checkout\AbstractCheckout
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Helper\Purchase                $rewardsPurchase
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Customer\Model\SessionFactory           $sessionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->sessionFactory = $sessionFactory;
        $this->context = $context;
        parent::__construct($rewardsPurchase, $customerSession, $checkoutSession, $context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('checkout/spend.phtml');
    }

    /**
     * @return int
     */
    public function getSpendPoints()
    {
        return $this->getPurchase()->getSpendPoints();
    }

    /**
     * @return int
     */
    public function getSpendAmount()
    {
        return $this->getPurchase()->getSpendAmount();
    }

    /**
     * @return int
     * @deprecated renamed
     */
    public function getPointsSpent()
    {
        return $this->getPurchase()->getSpendPoints();
    }

    /**
     * @return int
     * @deprecated renamed
     */
    public function getDiscount()
    {
        return $this->getPurchase()->getSpendAmount();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->sessionFactory->create()->isLoggedIn()) {
            return '';
        }

        return parent::_toHtml();
    }
}
