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

class SecureCheckout extends AbstractOnePageCheckout
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $secureCheckoutHelperConfig = $objectManager->create('\Magecheckout\SecureCheckout\Helper\Config');
        $this->secureCheckoutHelperConfig = $secureCheckoutHelperConfig;

        $this->_template = 'checkout/cart/usepoints_securecheckout_onestepcheckout.phtml';

        parent::__construct(
            $sessionFactory, $rewardsPurchase, $rewardsBalance, $customerSession, $checkoutSession, $context, $data
        );
    }

    /**
     * @return string
     */
    public function getApplySecureCheckoutUrl()
    {
        return $this->getUrl('rewards/checkout/ApplyPointsSecureCheckoutPost', ['_secure' => true]);
    }

    /**
     * @return bool
     */
    public function is1ColumnDesign()
    {
        return $this->secureCheckoutHelperConfig->getDesignConfig('page_layout') == '1column';
    }
}
