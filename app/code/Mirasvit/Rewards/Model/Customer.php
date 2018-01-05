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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer extends \Magento\Customer\Model\Customer
{
    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

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
     * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Customer\Model\Session                                     $customerSession
     * @param \Magento\Framework\Model\Context                                    $context
     * @param \Magento\Framework\Registry                                         $registry
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager
     * @param \Magento\Eav\Model\Config                                           $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                  $scopeConfig
     * @param \Magento\Customer\Model\Config\Share                                $configShare
     * @param \Magento\Customer\Model\AddressFactory                              $addressFactory
     * @param \Magento\Customer\Model\ResourceModel\Address\CollectionFactory     $addressesFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder                   $transportBuilder
     * @param \Magento\Customer\Api\GroupRepositoryInterface                      $groupRepository
     * @param \Magento\Framework\Encryption\EncryptorInterface                    $encryptor
     * @param \Magento\Framework\Stdlib\DateTime                                  $dateTime
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory                 $customerDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor                   $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper                             $dataObjectHelper
     * @param \Magento\Customer\Api\CustomerMetadataInterface                     $metadataService
     * @param \Magento\Framework\Indexer\IndexerRegistry                          $indexerRegistry
     * @param \Magento\Customer\Model\ResourceModel\Customer                      $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                       $resourceCollection
     * @param array                                                               $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressesFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Customer\Model\ResourceModel\Customer $resource,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $scopeConfig,
            $resource,
            $configShare,
            $addressFactory,
            $addressesFactory,
            $transportBuilder,
            $groupRepository,
            $encryptor,
            $dateTime,
            $customerDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $metadataService,
            $indexerRegistry,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return $this|bool
     */
    public function getCurrentCustomer()
    {
        $customer = $this->load($this->customerSession->getCustomerId());

        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    /**
     * @return \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection
     */
    public function getTransactions()
    {
        $transactions = $this->transactionCollectionFactory->create()
            ->addCustomerFilter($this->getId());

        return $transactions;
    }

    /**
     * @param string $transactionCode
     * @return bool|\Mirasvit\Rewards\Model\Transaction
     */
    public function getTransaction($transactionCode)
    {
        $transaction = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->getId())
            ->addFieldToFilter('code', $transactionCode)
            ->getFirstItem();

        if ($transaction->getId()) {
            return $transaction;
        }

        return false;
    }
}
