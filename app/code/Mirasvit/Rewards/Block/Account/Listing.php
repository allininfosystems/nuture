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



namespace Mirasvit\Rewards\Block\Account;

class Listing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

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
     * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Mirasvit\Rewards\Model\Config                                      $config
     * @param \Mirasvit\Rewards\Helper\Balance                                    $rewardsBalance
     * @param \Magento\Customer\Model\CustomerFactory                             $customerFactory
     * @param \Magento\Customer\Model\Session                                     $customerSession
     * @param \Magento\Framework\View\Element\Template\Context                    $context
     * @param array                                                               $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->config = $config;
        $this->rewardsBalance = $rewardsBalance;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->context = $context;
        parent::__construct($context, $data);

        $title = 'My Reward Points';
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__($title));
        }
        $this->pageConfig->getTitle()->set(__($title));
    }

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection
     */
    protected $_collection;

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTransactionCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'rewards.account_list_toolbar_pager'
            )->setCollection(
                $this->getTransactionCollection()
            );
            $this->setChild('pager', $pager);
            $this->getTransactionCollection()->load();
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
     * @return \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection|\Mirasvit\Rewards\Model\Transaction[]
     */
    public function getTransactionCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->transactionCollectionFactory->create()
                ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
                ->setOrder('created_at', 'desc')
                ;
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
     * @return int
     */
    public function getBalancePoints()
    {
        return $this->rewardsBalance->getBalancePoints($this->getCustomer());
    }

    /**
     * @return int
     */
    public function isCustomerSubscribed()
    {
        return (bool)$this->getCustomer()->getRewardsSubscription();
    }

    /**
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl('rewards/account/unsubscribe');
    }
}
