<?php

namespace Magestore\Rewardpoints\Helper;

class Customer extends Config {

    /**
     * reward account model
     * 
     * @var \Magestore\Rewardpoints\Model\Customer
     */
    protected $_rewardAccount = null;

    /**
     * current customer ID
     * 
     * @var int
     */
    protected $_customerId = null;

    /**
     * current working store ID
     * 
     * @var int
     */
    protected $_storeId = null;

    /**
     * get current customer model
     * 
     * @return \Magento\Customer\Model\Customer
     */
    protected $_customer = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_adminQuoteSession;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var Point
     */
    protected $pointHelper;

    /**
     * @var Calculation\Spending
     */
    protected $spendingHelper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    const XML_PATH_DISPLAY_TOPLINK = 'rewardpoints/display/toplink';
    const XML_PATH_REDEEMABLE_POINTS = 'rewardpoints/spending/redeemable_points';

    /**
     * Customer constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Backend\Model\Session\Quote $adminQuoteSession
     * @param \Magestore\Rewardpoints\Model\Customer $rewardCustomer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Config $globalConfig
     * @param Point $point
     * @param Calculation\Spending $spending
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Session\Quote $adminQuoteSession,
        \Magestore\Rewardpoints\Model\Customer $rewardCustomer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magestore\Rewardpoints\Helper\Point $point,
        \Magento\Framework\App\State $appState,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $spending

    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_adminQuoteSession = $adminQuoteSession;
        $this->_rewardAccount = $rewardCustomer;
        $this->_storeManager = $storeManager;
        $this->helper = $globalConfig;
        $this->_appState = $appState;
        $this->pointHelper = $point;
        $this->spendingHelper = $spending;
    }


    public function getCustomer() {
        if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
            $this->_customer = $this->_adminQuoteSession->getCustomer();
            return $this->_customer;
        }
        if ($this->_customerSession->getCustomerId()) {
            $this->_customer = $this->_customerSession->getCustomer();
            return $this->_customer;
        }
        return $this->_customer;
    }

    /**
     * get current customer ID
     *
     * @return int
     */
    public function getCustomerId() {
        if (is_null($this->_customerId)) {
			$customerId = 0;
            if($this->_appState->getAreaCode() ==  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE){
                $this->_customerId = $this->_adminQuoteSession->getCustomerId();
                return $this->_customerId;
            } else  {
				if($this->_customerSession->isLoggedIn())
					$customerId = $this->_customerSession->getCustomerId();
            }
            if ($customerId) {
                $this->_customerId = $customerId;
            } else {
                $this->_customerId = 0;
            }
        }
        return $this->_customerId;
    }

    /**
     * get current working store id, used when checkout
     *
     * @return int
     */
    public function getStoreId() {
        if (is_null($this->_storeId)) {
            if ($this->_storeManager->isSingleStoreMode()) {
                $this->_storeId = $this->_storeManager->getStore()->getId();
            } else
            if($this->_appState->getAreaCode() ==  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE){
                $this->_storeId = $this->_adminQuoteSession->getStoreId();
            } else {
                $this->_storeId = $this->_storeManager->getStore()->getId();
            }
        }
        return $this->_storeId;
    }

    /**
     * get current reward points customer account
     *
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccount() {
        if (!$this->_rewardAccount->getId()) {
            if ($this->getCustomerId()) {
                $this->_rewardAccount->load($this->getCustomerId(), 'customer_id');
                $this->_rewardAccount->setData('customer', $this->getCustomer());
            }
        }

        return $this->_rewardAccount;
    }

    /**
     * get Reward Points Account by Customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccountByCustomer($customer) {
        $rewardAccount = $this->getAccountByCustomerId($customer->getId());
        if (!$rewardAccount->hasData('customer')) {
            $rewardAccount->setData('customer', $customer);
        }
        return $rewardAccount;
    }

    /**
     * get Reward Points Account by Customer ID
     *
     * @param int $customerId
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccountByCustomerId($customerId = null) {
        if (empty($customerId) || $customerId == $this->getCustomerId()
        ) {
            return $this->getAccount();
        }
        return $this->_rewardAccount->load($customerId, 'customer_id');
    }

    /**
     * get reward points balance of current customer
     *
     * @return int
     */
    public function getBalance() {
        return $this->getAccount()->getPointBalance();
    }

    /**
     * get string of points balance formated
     *
     * @return string
     */
    public function getBalanceFormated() {

        return $this->pointHelper->format(
            $this->getBalance(), $this->getStoreId()
        );
    }

    /**
     * get string of points balance formated
     * Balance is estimated after customer use point to spent
     *
     * @return string
     */
    public function getBalanceAfterSpentFormated() {
        return $this->pointHelper->format(
                        $this->getBalance() - $this->spendingHelper->getTotalPointSpent(), $this->getStoreId()
        );
    }

    /**
     * check show customer reward points on top link
     *
     * @param type $store
     * @return boolean
     */
    public function showOnToplink($store = null) {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_TOPLINK, $store);
    }

    /**
     * check customer can use point to spend for order or not
     *
     * @param type $store
     * @return boolean
     */
    public function isAllowSpend($store = null) {
        $minPoint = (int) $this->getSpendingConfig('redeemable_points', $store);
        if ($minPoint > $this->getBalance()) {
            return false;
        }
        return true;
    }

    

}
