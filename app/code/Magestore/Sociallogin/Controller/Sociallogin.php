<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller;

abstract class Sociallogin extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelperData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @var \Magestore\Sociallogin\Helper\Data
     */
    protected $_helperData;

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     *
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */

    protected $_customerSocialCollectionFactory;

    /**
     *
     * @var \Magestore\Sociallogin\Model\CustomerFactory
     */
    protected $_customerSocialFactory;
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */

    protected $_objectManager;
    /**
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;
    /**
     *
     * @var \Magestore\Sociallogin\Model\Resource\Vklogin\CollectionFactory
     */
    protected $_vkloginCollectionFactory;

    /**
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Sociallogin\Helper\Data $helperData
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Sociallogin\Helper\Data $helperData,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Sociallogin\Model\ResourceModel\Customer\CollectionFactory $customerSocialCollectionFactory,
        \Magestore\Sociallogin\Model\ResourceModel\Vklogin\CollectionFactory $vkloginCollectionFactory,
        \Magestore\Sociallogin\Model\CustomerFactory $customerSocialFactory,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_helperData = $helperData;
        $this->_session = $session;
        $this->_objectManager = $context->getObjectManager();
        $this->_customerFactory = $customerFactory;
        $this->_customerSocialCollectionFactory = $customerSocialCollectionFactory;
        $this->_vkloginCollectionFactory = $vkloginCollectionFactory;
        $this->_customerSocialFactory = $customerSocialFactory;

        parent::__construct($context);
    }

    /**
     * Define and return target URL after logging in
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _loginPostRedirect()
    {

        $selecturl = $this->_helperData->getConfig(('general/select_redirect_page'), $this->_storeManager->getStore()->getId());
        switch ($selecturl) {
            case 0:
                return $this->_storeManager->getStore()->getUrl('customer/account');
                break;
            case 1:
                return $this->_storeManager->getStore()->getUrl('checkout/cart');
                break;
            case 2:
                return $this->_storeManager->getStore()->getUrl();
                break;
            case 3:
                return $this->_getSingtone()->getCurrentPage();
                break;
            case 4:
                return $this->_helperData->getConfig(('general/custom_page'), $this->_storeManager->getStore()->getId());
                break;
            default:
                return $this->_storeManager->getStore()->getUrl('customer/account');
                break;
        }

        /**
         * Retrieve customer session model object
         *
         * @return \Magento\Customer\Model\Session
         */

    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }

    protected function _getSingtone()
    {
        return $this->_session;
    }

}