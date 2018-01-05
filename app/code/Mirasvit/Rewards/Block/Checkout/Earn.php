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

class Earn extends \Mirasvit\Rewards\Block\Checkout\AbstractCheckout
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Magento\Customer\Model\Session                  $sessionFactory
     * @param \Mirasvit\Rewards\Helper\Purchase                $rewardsPurchase
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $sessionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->sessionFactory = $sessionFactory;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->context = $context;
        parent::__construct($this->rewardsPurchase, $sessionFactory, $checkoutSession, $context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('checkout/earn.phtml');
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->sessionFactory->isLoggedIn()) {
            return '';
        }

        return parent::_toHtml();
    }
}
