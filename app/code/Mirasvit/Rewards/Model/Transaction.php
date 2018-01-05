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
 * @method \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection getCollection()
 * @method \Mirasvit\Rewards\Model\Transaction load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Transaction setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Transaction setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Transaction getResource()
 * @method int getCustomerId()
 * @method \Mirasvit\Rewards\Model\Transaction setCustomerId(int $entityId)
 */
class Transaction extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DISCARDED = 2;

    const CACHE_TAG = 'rewards_transaction';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_transaction';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_transaction';

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
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

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
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface             $localeDate
     * @param \Magento\Framework\Model\Context                                 $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource          $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                    $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->localeDate = $localeDate;
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
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Transaction');
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
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer = null;

    /**
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            if ($this->getCustomerId()) {
                $this->_customer = $this->customerCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('entity_id', $this->getCustomerId())
                    ->getFirstItem();
            } else {
                $this->_customer = false;
            }
        }

        return $this->_customer;
    }

    /************************/

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getExpiresAtFormatted()
    {
        $expires = $this->getData('expires_at');
        if ($expires) {
            return $this->localeDate->formatDate($expires, \IntlDateFormatter::MEDIUM);
        } else {
            return __('-');
        }
    }

    /**
     * @return int
     */
    public function getDaysLeft()
    {
        if ($expires = $this->getData('expires_at')) {
            $diff = strtotime($expires) - time();
            $days = (int) ($diff / 60 / 60 / 24);

            return $days;
        }
    }
}
