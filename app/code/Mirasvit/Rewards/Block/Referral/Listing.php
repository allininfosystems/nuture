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



namespace Mirasvit\Rewards\Block\Referral;

class Listing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Rewards\Helper\Referral
     */
    protected $helper;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @param \Mirasvit\Rewards\Helper\Referral                                $helper
     * @param \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory
     * @param \Mirasvit\Rewards\Model\Config                                   $config
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Customer\Model\Session                                  $customerSession
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Magento\Framework\Locale\ResolverInterface                      $localeResolver
     * @param array                                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Referral $helper,
        \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->config = $config;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->localeResolver = $localeResolver;
        parent::__construct($context, $data);

        $title = 'Referrals List';
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__($title));
        }
        $this->pageConfig->getTitle()->set(__($title));
    }

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection
     */
    protected $_collection;

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReferralCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'rewards.account_referral_list_toolbar_pager'
            )->setCollection(
                $this->getReferralCollection()
            );
            $this->setChild('pager', $pager);
            $this->getReferralCollection()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \Mirasvit\Rewards\Model\Referral[]|\Mirasvit\Rewards\Model\ResourceModel\Referral\Collection
     */
    public function getReferralCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->referralCollectionFactory->create()
                ->addFieldToFilter('main_table.customer_id', $this->getCustomer()->getId())
                ->setOrder('created_at', 'desc');
        }

        return $this->_collection;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }

    /**
     * @return string
     */
    public function getShareUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('r/'.$this->helper->getReferralLinkId());
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        $locale = $this->context->getStoreManager()->getStore()->getLocaleCode();
        if (!$locale) {
            $locale = 'en';
        }

        return $locale;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->config->getFacebookAppId();
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getFacebookIsActive()
    {
        return $this->config->getFacebookIsActive();
    }
}
