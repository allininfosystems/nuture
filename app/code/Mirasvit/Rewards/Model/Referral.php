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



namespace Mirasvit\Rewards\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection|\Mirasvit\Rewards\Model\Referral[] getCollection()
 * @method \Mirasvit\Rewards\Model\Referral load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Referral setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Referral setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Referral getResource()
 * @method int getStoreId()
 * @method \Mirasvit\Rewards\Model\Referral setStoreId(int $storeId)
 * @method int getCustomerId()
 * @method \Mirasvit\Rewards\Model\Referral setCustomerId(int $entityId)
 * @method int getNewCustomerId()
 * @method \Mirasvit\Rewards\Model\Referral setNewCustomerId(int $entityId)
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Referral extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'rewards_referral';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_referral';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_referral';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Referral\StatusFactory
     */
    protected $configSourceReferralStatusFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail
     */
    protected $rewardsMail;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Magento\Store\Model\StoreFactory                                $storeFactory
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Mirasvit\Rewards\Model\Config\Source\Referral\StatusFactory     $configSourceReferralStatusFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\Session                                  $session
     * @param \Mirasvit\Rewards\Helper\Mail                                    $rewardsMail
     * @param \Magento\Framework\UrlInterface                                  $urlManager
     * @param \Magento\Framework\Model\Context                                 $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource          $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                    $resourceCollection
     * @param array                                                            $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Mirasvit\Rewards\Model\Config\Source\Referral\StatusFactory $configSourceReferralStatusFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\Session $session,
        \Mirasvit\Rewards\Helper\Mail $rewardsMail,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeFactory = $storeFactory;
        $this->customerFactory = $customerFactory;
        $this->configSourceReferralStatusFactory = $configSourceReferralStatusFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->session = $session;
        $this->rewardsMail = $rewardsMail;
        $this->urlManager = $urlManager;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Referral');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @return bool|\Magento\Store\Model\Store
     */
    public function getStore()
    {
        if (!$this->getStoreId()) {
            return false;
        }
        if ($this->_store === null) {
            $this->_store = $this->storeFactory->create()->load($this->getStoreId());
        }

        return $this->_store;
    }

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer = null;

    /**
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
        if ($this->_customer === null) {
            // $this->_customer = $this->customerFactory->create()->load($this->getCustomerId());
            $this->_customer = $this->customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $this->getCustomerId())
                ->getFirstItem();
        }

        return $this->_customer;
    }

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_newCustomer = null;

    /**
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getNewCustomer()
    {
        if (!$this->getNewCustomerId()) {
            return false;
        }
        if ($this->_newCustomer === null) {
            $this->_newCustomer = $this->customerFactory->create()->load($this->getNewCustomerId());
        }

        return $this->_newCustomer;
    }

    /************************/

    /**
     * @param string $message
     * @return void
     */
    public function sendInvitation($message)
    {
        $this->rewardsMail->sendReferralInvitationEmail($this, $message);
        $this->setStatus(Config::REFERRAL_STATUS_SENT);
        $this->save();
    }

    /**
     * @return string
     */
    public function getInvitationUrl()
    {
        return $this->urlManager->getUrl('rewards/referral/invite', ['id' => $this->getId()]);
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = $this->configSourceReferralStatusFactory->create()->toArray();
        if (isset($statuses[$this->getStatus()])) {
            return $statuses[$this->getStatus()];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = parent::getName();
        if (!$name && ($customer = $this->getNewCustomer())) {
            $name = $customer->getName();
        }

        return $name;
    }

    /**
     * @param string                                   $status
     * @param bool|int                                 $newCustomerId
     * @param bool|\Mirasvit\Rewards\Model\Transaction $transaction
     *
     * @return void
     */
    public function finish($status, $newCustomerId = false, $transaction = false)
    {
        $this->setStatus($status);
        if ($newCustomerId) {
            $this->setNewCustomerId($newCustomerId);
        }
        if ($transaction) {
            $this->setLastTransactionId($transaction->getId())
                ->setPointsAmount($transaction->getAmount() + (int) $this->getPointsAmount());
        }
        $this->save();
        $this->session->setReferral(0);
    }
}
