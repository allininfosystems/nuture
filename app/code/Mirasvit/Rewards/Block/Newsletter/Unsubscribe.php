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


namespace Mirasvit\Rewards\Block\Newsletter;

class Unsubscribe extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'newsletter/subscription.phtml';

    /**
     * @var \Mirasvit\Rewards\Helper\Mail
     */
    protected $mailHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Mirasvit\Rewards\Model\Config                   $config
     * @param \Mirasvit\Rewards\Helper\Mail                    $mailHelper
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Mail $mailHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config          = $config;
        $this->mailHelper      = $mailHelper;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return $this->config->getNotificationPointsExpireEmailTemplate() != "none";
    }

    /**
     * @return bool
     */
    public function isRewardsSubscribed()
    {
        return $this->mailHelper->isCustomerSubscribed($this->getCustomer());
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}
